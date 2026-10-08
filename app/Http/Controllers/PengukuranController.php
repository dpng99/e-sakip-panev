<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Indikator;
use App\Models\Pengukuran;

class PengukuranController extends Controller
{ 
    /**
     * Memastikan hanya pengguna yang berwenang dapat mengakses menu Pengukuran.
     */
    protected function checkAccess()
    {
        $level = session('id_sakip_level');
        $satker = session('id_satker');
        $allowed = in_array($satker, ['admin', '999999']) || in_array((int)$level, [1, 2, 3, 4, 99]);
        if (!$allowed) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki akses ke Menu Pengukuran.');
        }
    }

   public function index(Request $request)
    {
        $this->checkAccess();

        $id_bidang = $request->get('id_bidang');
        $tahun = session('tahun_terpilih');
        $id_satker = session('id_satker');

        $indikators = [];
        $data = [];

        if ($id_bidang) {
            $indikators = Indikator::where('id_bidang', $id_bidang)->get();

            foreach ($indikators as $indikator) {
                $subIndikators = explode(',', $indikator->sub_indikator);
                $pengukuranData = Pengukuran::where('indikator_id', $indikator->id)
                    ->where('id_satker', $id_satker)
                    ->where('tahun', $tahun)
                    ->get();

                $data[$indikator->id] = [
                    'nama' => $indikator->indikator_nama,
                    'sub' => []
                ];

                foreach ($subIndikators as $sub) {
                    $sub = trim($sub);
                    $data[$indikator->id]['sub'][$sub] = $pengukuranData
                        ->where('sub_indikator', $sub)
                        ->where('id_satker', $id_satker)
                        ->keyBy('bulan'); // <--- agar mudah akses berdasarkan bulan
                }
            }
        }

        return view('kelola.pengukuran', compact('data', 'indikators', 'tahun'));
    }


  public function store(Request $request)
{
    $this->checkAccess();

    $subIndikatorList = $request->input('sub_indikator_list');

    if (!is_array($subIndikatorList)) {
        return redirect()->back()->withErrors('Tidak ada data yang dikirim.');
    }

    $id_satker = session('id_satker');
    $tahun     = session('tahun_terpilih');
    $khusus = session('tw4_khusus', false) ? 1 : 0;

    $bulanMap = [
        'JANUARI' => 1, 'FEBRUARI' => 2, 'MARET' => 3, 'APRIL' => 4,
        'MEI' => 5, 'JUNI' => 6, 'JULI' => 7, 'AGUSTUS' => 8,
        'SEPTEMBER' => 9, 'OKTOBER' => 10, 'NOVEMBER' => 11, 'DESEMBER' => 12,
    ];

    $triwulanMap = [
        'TW1' => [3],
        'TW2' => [6],
        'TW3' => [9],
        'TW4' => [12],
    ];

    // helper lokal untuk normalisasi angka
    $normalizeNumber = function ($val) {
        if ($val === null || $val === '' || $val === '-') {
            return null;
        }
        // hilangkan pemisah ribuan, ubah koma jadi titik (desimal)
        $val = str_replace('.', '', $val);
        $val = str_replace(',', '.', $val);
        return (float) $val;
    };

    foreach ($subIndikatorList as $subIndikator) {
        $indikatorId = (string) $request->input("indikator_id.$subIndikator");
        $indikator   = Indikator::find($indikatorId);

        if (!$indikator) {
            continue; // skip kalau tidak valid
        }

        // === Simpan Sisa Tahun Lalu (kalau ada) ===
        $sisaTahunLalu = $normalizeNumber($request->input("sisa_tahun_lalu.$subIndikator"));

        $pengukuranSisa = \App\Models\Pengukuran::firstOrNew([
            'indikator_id'  => $indikatorId,
            'id_satker'     => $id_satker,
            'tahun'         => $tahun,
            'sub_indikator' => $subIndikator,
            'bulan'         => 1, // Januari
            'khusus'        => $khusus,
        ]);
        $pengukuranSisa->sisa_tahun_lalu = $sisaTahunLalu;
        $pengukuranSisa->save();

        // === Proses Bulanan ===
        $labels = [];
        if (!empty($indikator->indikator_penghitungan)) {
            $labels = array_map('trim', explode(',', strtolower($indikator->indikator_penghitungan)));
        }
        if (empty($labels)) {
            $labels = ['ditangani', 'diselesaikan'];
        }

        foreach ($bulanMap as $bulanNama => $bulanAngka) {
            $values = [];

            foreach ($labels as $label) {
                $val = $normalizeNumber($request->input("$label.$subIndikator.$bulanNama"));
                $values[] = $val;
            }

            // gabungkan dengan ;
            $capaian = implode(';', array_map(fn($v) => $v ?? '', $values));

            $pengukuran = \App\Models\Pengukuran::firstOrNew([
                'indikator_id'  => $indikatorId,
                'id_satker'     => $id_satker,
                'tahun'         => $tahun,
                'sub_indikator' => $subIndikator,
                'bulan'         => $bulanAngka,
                'khusus'        => $khusus,
            ]);
            $pengukuran->perhitungan = $capaian !== '' ? $capaian : null;

            if ($bulanAngka == 1) {
                $pengukuran->sisa_tahun_lalu = $sisaTahunLalu;
            }
            $pengukuran->save();
        }

        // === Proses Triwulan ===
        foreach ($triwulanMap as $tw => $bulanList) {
            foreach ($labels as $label) {
                $nilai = $normalizeNumber($request->input("$label.$subIndikator.$tw"));

                foreach ($bulanList as $bulanAngka) {
                    $pengukuran = \App\Models\Pengukuran::firstOrNew([
                        'indikator_id'  => $indikatorId,
                        'id_satker'     => $id_satker,
                        'tahun'         => $tahun,
                        'sub_indikator' => $subIndikator,
                        'bulan'         => $bulanAngka,
                        'khusus'        => $khusus,
                    ]);
                    $pengukuran->capaian = $nilai; // jika $nilai null → akan overwrite ke null
                    $pengukuran->save();
                }
            }
        }
    }

    return redirect()->back()->with('success', 'Data pengukuran berhasil disimpan atau diperbarui.');
}


    public function form($id)
    {
        $this->checkAccess();

        $indikator = Indikator::findOrFail($id);
        return view('pengukuran.form_pengukuran', compact('indikator'));
    }

    public function getPengukuran($indikatorId)
    {
        $this->checkAccess();

        $idSatker = session('id_satker') ?? auth()->user()?->id_satker;
        $tahun = session('tahun_terpilih');
        $khusus = session('tw4_khusus', false) ? 1 : 0;

        $query = \App\Models\Pengukuran::where('indikator_id', (string) $indikatorId)
            ->where('id_satker', $idSatker);

        if ($tahun) {
            $query->where('tahun', $tahun);
        }
        $query->where('khusus', $khusus);

        $pengukuran = $query->get(['sub_indikator', 'bulan', 'perhitungan', 'sisa_tahun_lalu', 'capaian']);

        return response()->json($pengukuran);
    }

//Indikator untuk menu pengukuran ada di pelaporan controller
//    public function getSubindikator($rumpun)
//     {
//         $tahun = date('Y');
//         $level = session('id_sakip_level');

//         $indikators = Indikator::where('link', $rumpun)
//             ->where(function ($query) use ($tahun) {
//                 $query->where('tahun', 'LIKE', "%$tahun%");
//             })
//             ->where(function ($query) use ($level) {
//                 if ($level == 1) {
//                     $query->whereIn('lingkup', [0, 1]);
//                 } elseif ($level == 2) {
//                     $query->whereIn('lingkup', [0, 2, 5, 7]);
//                 } elseif ($level == 3) {
//                     $query->whereIn('lingkup', [0, 3, 5, 6, 7]);
//                 } elseif ($level == 4) {
//                     $query->whereIn('lingkup', [0, 4, 6]);
//                 }
//             })
//             ->get();

//         return response()->json($indikators);
//     }

}