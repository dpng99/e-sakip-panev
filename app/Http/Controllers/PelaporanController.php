<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Lkjip;
use App\Models\RapatStaffEka;
use App\Models\Indikator;
use App\Models\TargetPK;
use App\Models\Pengukuran;
use App\Models\Bidang;
use App\Models\SasaranProgram;
use App\Models\IndikatorKinerjaProgram;
use App\Models\TargetIkp;
use App\Models\PelaporanIkp;
use Carbon\Carbon;

class PelaporanController extends Controller
{
    public function index(Request $request)
    {
        // Cek apakah tahun sudah dipilih
        if (!session()->has('tahun_terpilih')) {
            return redirect()->route('pilih.tahun');
        }
        $level = session('id_sakip_level');
        $tahun = session('tahun_terpilih');
        $khusus = session('tw4_khusus', false) ? 1 : 0;
        $idSatker = session('id_satker');
        $id_bidang = $request->get('id_bidang');
        $indikators = [];
        $data = [];

        // Ambil data dari database
        $lkjipFiles = Lkjip::where('id_periode', $tahun)
            ->where('id_satker', $idSatker)
            ->orderBy('id_perubahan', 'desc')
            ->get();

        $rapatStaffEkaFiles = RapatStaffEka::where('id_satker', $idSatker)
            ->where('id_periode', $tahun)
            ->orderBy('id_tglupload', 'desc')
            ->get();

        $bidangs = in_array($level, [2, 3])
            ? Bidang::where('bidang_lokasi', $level)
            ->where('bidang_level', '!=', null)
            ->orderBy('bidang_level', 'asc')
            ->get()
            : [];

        if ($id_bidang) {
            $indikators = Indikator::where('id_bidang', $id_bidang)->get();

            foreach ($indikators as $indikator) {
                $subIndikators = explode(',', $indikator->sub_indikator);
                $pengukuranData = Pengukuran::where('indikator_id', $indikator->id)
                    ->where('id_satker', $idSatker)
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
                        ->where('id_satker', $idSatker)
                        ->keyBy('bulan'); // <--- agar mudah akses berdasarkan bulan
                }
            }
        }

        // $rapatStaffEkaFiles = RapatStaffEka::orderBy('id_tglupload', 'desc')->get();

        return view('kelola.pelaporan', ['tahun' => $tahun, 'bidangs' => $bidangs, 'lkjipFiles' => $lkjipFiles,  'rapatStaffEkaFiles' => $rapatStaffEkaFiles,]);
    }

    // public function getIndikatorByBidang($id_bidang)
    // {
    //     $indikator = Indikator::where('id_bidang', $id_bidang)
    //         ->select('id', 'indikator_nama', 'sub_indikator')
    //         ->get();

    //     return response()->json($indikator);
    // }

//  //Indikator untuk menu pengukuran
    public function getSubIndikator($rumpun)
    {
        $tahun = session('tahun_terpilih');
        $level = session('id_sakip_level');
        $id_satker = session('id_satker');
        if (in_array((int)$level, [0, 99]) && $id_satker) {
            $satkerUser = DB::table('sinori_login')->where('id_satker', $id_satker)->first();
            if ($satkerUser && !empty($satkerUser->id_sakip_level)) {
                $level = $satkerUser->id_sakip_level;
            }
        }
        $khusus = session('tw4_khusus', false) ? 1 : 0;

        // Ambil semua indikator dengan filter rumpun dan tahun
        $indikators = Indikator::where('link', $rumpun)
            ->where('tahun', 'LIKE', "%$tahun%")
            ->where('khusus', $khusus)
            ->get();

        // Filter berdasarkan lingkup & level
        $filtered = $indikators->filter(function ($indikator) use ($level) {
            switch ($indikator->lingkup ?? 0) {
                case 0:
                    return in_array($level, [1, 2, 3, 4]);
                case 1:
                    return $level == 1;
                case 2:
                    return $level == 2;
                case 3:
                    return $level == 3;
                case 4:
                    return $level == 4;
                case 5:
                    return in_array($level, [2, 3]);
                case 6:
                    return in_array($level, [3, 4]);
                case 7:
                    return in_array($level, [2, 3, 4]);
                default:
                    return false;
            }
        })->values(); // reset keys

        return response()->json($filtered);
    }

    public function getSubIndikator2($rumpun, Request $request)
    {
        $id_satker = session('id_satker');
        $tw = $request->query('triwulan', 1);
        $tahun = session('tahun_terpilih');
        $bulan_awal = ($tw - 1) * 3 + 1;
        $bulan_akhir = $bulan_awal + 2;
        $level = session('id_sakip_level');
        if (in_array((int)$level, [0, 99]) && $id_satker) {
            $satkerUser = DB::table('sinori_login')->where('id_satker', $id_satker)->first();
            if ($satkerUser && !empty($satkerUser->id_sakip_level)) {
                $level = $satkerUser->id_sakip_level;
            }
        }
        $khusus = session('tw4_khusus', false) ? 1 : 0;

        // === Ambil indikator sesuai rumpun, tahun, dan lingkup level ===
        $indikators = Indikator::where('link', $rumpun)
            ->where(function ($query) use ($tahun) {
                $query->where('tahun', 'LIKE', "%$tahun%");
            })
            ->where('khusus', $khusus)
            ->where(function ($query) use ($level) {
                if ($level == 1) {
                    $query->whereIn('lingkup', [0, 1]);
                } elseif ($level == 2) {
                    $query->whereIn('lingkup', [0, 2, 5, 7]);
                } elseif ($level == 3) {
                    $query->whereIn('lingkup', [0, 3, 5, 6, 7]);
                } elseif ($level == 4) {
                    $query->whereIn('lingkup', [0, 4, 6, 7]);
                }
            })
            ->get();

        $data = [];

        foreach ($indikators as $indikator) {
            $indikatorId = (string) $indikator->id;
            $persentase = 0;

            // === Tentukan label penghitungan (default: Ditangani, Diselesaikan) ===
            $labels = [];
            if (!empty($indikator->indikator_penghitungan)) {
                $labels = array_map('trim', explode(',', strtolower($indikator->indikator_penghitungan)));
            }
            if (empty($labels)) {
                $labels = ['ditangani', 'diselesaikan'];
            }

            if (count($labels) == 1) {
                // === MODE 1 LABEL ===
                // Ambil capaian dari bulan terakhir id_triwulan(misalnya 3,6,9,12)
                $lastMonth = $bulan_akhir;

                $persentase = DB::table('pengukuran')
                    ->where('id_satker', $id_satker)
                    ->where('tahun', $tahun)
                    ->where('indikator_id', $indikatorId)
                    ->where('bulan', $lastMonth)
                    ->where('khusus', $khusus)
                    ->orderBy('id', 'desc')
                    ->value('capaian') ?? 0;
            } elseif (count($labels) > 1) {
                // === MODE MULTI LABEL (pembilang/penyebut dari field perhitungan) ===
                $rows = DB::table('pengukuran')
                    ->where('id_satker', $id_satker)
                    ->where('tahun', $tahun)
                    ->where('indikator_id', $indikatorId)
                    ->where('khusus', $khusus)
                    ->whereBetween('bulan', [1, $bulan_akhir]) // kumulatif s.d akhir TW
                    ->get(['sub_indikator', 'perhitungan']);

                $persentaseSub = [];

                foreach ($rows->groupBy('sub_indikator') as $subIndikator => $dataRow) {
                    $pembilang = 0;
                    $penyebut = 0;

                    foreach ($dataRow as $row) {
                        if (!empty($row->perhitungan) && str_contains($row->perhitungan, ';')) {
                            [$a, $b] = explode(';', $row->perhitungan);

                            // Anggap format "penyebut;pembilang"
                            $penyebut += (float) $a;
                            $pembilang += (float) $b;
                        }
                    }

                    if ($penyebut > 0) {
                        $persentaseSub[] = round(($pembilang / $penyebut) * 100, 2);
                    }
                }

                // Ambil rata-rata semua persentase sub indikator
                $persentase = count($persentaseSub) > 0
                    ? round(array_sum($persentaseSub) / count($persentaseSub), 2)
                    : 0;
            }

            // === Ambil Target PK ===
            $target_pk = DB::table('target')
                ->where('id_satker', $id_satker)
                ->where('tahun', $tahun)
                ->where('indikator_id', $indikatorId)
                ->where('khusus', $khusus)
                ->value('target_tahun') ?? 0;

            // === Hitung capaian PK ===
            $capaian_pk = $target_pk > 0
                ? round(($persentase / $target_pk) * 100, 2)
                : 0;

            // === Ambil faktor & langkah (dari bulan terakhir triwulan) ===
            $first = DB::table('pengukuran')
                ->where('id_satker', $id_satker)
                ->where('tahun', $tahun)
                ->where('indikator_id', $indikatorId)
                ->where('bulan', $bulan_akhir)
                ->where(function ($q) {
                    $q->whereNotNull('faktor')
                        ->orWhereNotNull('langkah_optimalisasi');
                })
                ->where('khusus', $khusus)
                ->orderBy('bulan', 'desc')
                ->orderBy('id', 'desc')
                ->first();


            $data[] = [
                'indikator_id' => $indikatorId,
                'indikator_nama' => $indikator->indikator_nama,
                'indikator_penghitungan' => $indikator->indikator_penghitungan ?: 'Ditangani, Diselesaikan',
                'persentase' => (float) $persentase,
                'target_pk' => (float) $target_pk,
                'capaian_pk' => (float) $capaian_pk,
                'faktor' => $first->faktor ?? '',
                'langkah' => $first->langkah_optimalisasi ?? '',
            ];
        }

        return response()->json($data);
    }



    public function simpanKeterangan(Request $request)
    {
        $khusus = session('tw4_khusus', false) ? 1 : 0;
        $request->validate([
            'indikator_id' => 'required',
            'faktor' => 'nullable|string',
            'langkah' => 'nullable|string',
            'triwulan' => 'required|integer|min:1|max:4',
        ]);

        $bulan_akhir = ($request->triwulan - 1) * 3 + 3;
        $indikatorId = (string) $request->indikator_id;
        $idSatker = session('id_satker');
        $tahun = session('tahun_terpilih');

        $pengukuran = Pengukuran::where('id_satker', $idSatker)
            ->where('tahun', $tahun)
            ->where('indikator_id', $indikatorId)
            ->where('bulan', $bulan_akhir)
            ->where('khusus', $khusus)
            ->first();

        if ($pengukuran) {
            $pengukuran->faktor = $request->faktor;
            $pengukuran->langkah_optimalisasi = $request->langkah;
            $pengukuran->save();

            return response()->json(['status' => 'success']);
        } else {
            $indikator = Indikator::find($indikatorId);
            $subIndikator = $indikator ? explode(',', $indikator->sub_indikator)[0] : '';

            Pengukuran::create([
                'indikator_id' => $indikatorId,
                'id_satker' => $idSatker,
                'tahun' => $tahun,
                'bulan' => $bulan_akhir,
                'khusus' => $khusus,
                'sub_indikator' => trim($subIndikator),
                'faktor' => $request->faktor,
                'langkah_optimalisasi' => $request->langkah,
            ]);

            return response()->json(['status' => 'success']);
        }
    }

    public function uploadLkjip(Request $request)
    {
        $tahun = session('tahun_terpilih');
        $request->validate([
            'lkjip_file' => 'required|mimes:pdf|max:4096', // Max 4MB
            'id_triwulan' => 'required|in:TW 1,TW 2,TW 3,TW 4',
        ]);

        $idSatker = session('id_satker'); // Ambil id_satker dari session
        $id_triwulan = $request->input('id_triwulan');

        // Cek id_perubahan yang sudah ada
        $latestLkjip = Lkjip::where('id_satker', $idSatker)
            ->where('id_periode', $tahun)
            ->where('id_triwulan', $id_triwulan)
            ->orderBy(DB::raw('CAST(id_perubahan AS UNSIGNED)'), 'desc')
            ->first();

        // Tentukan id_perubahan
        $id_perubahan = $latestLkjip ? $latestLkjip->id_perubahan + 1 : 0;

        // Upload file ke folder public/uploads/lkjip
        $file = $request->file('lkjip_file');
        $fileName = 'lkjip_' . $tahun . '_' . $id_perubahan . '_' . $id_triwulan . '.pdf';
        $file->move(public_path('uploads/repository/' . $idSatker), $fileName);

        // Simpan data ke database
        Lkjip::create([
            'id_satker' => $idSatker,
            'id_periode' => $tahun,
            'id_triwulan' => $id_triwulan,
            'id_perubahan' => $id_perubahan,
            'id_filename' => $fileName,
            'id_tglupload' => now()->format('d/m/Y h:i A'),

        ]);
        return redirect()->route('pelaporan')->with(['success-lkjip' => 'File LKJiP berhasil diupload.', 'active_tab' => 'lkjip']);
    }


    // public function deleteLkjip($id)
    // {
    //     $idSatker = session('id_satker'); 
    //     $file = Lkjip::findOrFail($id);

    //     // Hapus file dari storage
    //     $filePath = public_path('uploads/repository/' . $idSatker . '/'. $file->filename);
    //     if (file_exists($filePath)) {
    //         unlink($filePath);
    //     }

    //     // Hapus dari database
    //     $file->delete();

    //     return redirect()->back()->with('success-lkjip', 'File berhasil dihapus.');
    // }

    public function uploadRapatStaffEka(Request $request)
    {
        $request->validate([
            'id_triwulan' => 'required|in:TW 1,TW 2,TW 3,TW 4',
            'rapat_file' => 'required|mimes:pdf|max:4096',
        ]);

        $tahun = session('tahun_terpilih');
        $idSatker = session('id_satker');
        $triwulan = $request->input('id_triwulan');

        // Ambil versi terbaru dari database
        $latestRapat = RapatStaffEka::where('id_satker', $idSatker)
            ->where('id_periode', $tahun)
            ->where('id_triwulan', $triwulan)
            ->orderBy(DB::raw('CAST(id_perubahan AS UNSIGNED)'), 'desc')
            ->first();

        $id_perubahan = $latestRapat ? $latestRapat->id_perubahan + 1 : 0;

        if ($request->hasFile('rapat_file')) {
            $file = $request->file('rapat_file');
            $filename = 'rastaff_' . $tahun . '_' . $id_perubahan . '_' . $triwulan . '.pdf';
            $file->move(public_path('uploads/repository/' . $idSatker), $filename);

            RapatStaffEka::create([
                'id_periode' => $tahun,
                'id_satker' => $idSatker,
                'id_perubahan' => $id_perubahan,
                'id_filename' => $filename,
                'id_tglupload' => now()->format('d/m/Y h:i A'),
                // Format dengan AM/PM
                'id_triwulan' => $triwulan,
            ]);

            return redirect()->route('pelaporan')->with(['success-rastaff' => 'File Rapat Staff EKA berhasil diunggah.', 'active_tab' => 'rapat-staff-eka']);
        }

        return back()->with('error', 'Gagal mengunggah file.');
    }

    /**
     * Mendapatkan data SP, IKP, Target, Capaian, dan Faktor/Langkah untuk Pelaporan Capaian Kinerja Level 1
     */
    public function getIkpData($idSatkerBidang, Request $request)
    {
        $tw = (int)$request->query('triwulan', 1);
        $tahun = session('tahun_terpilih');
        $bulan_akhir = $tw * 3; // TW1=3, TW2=6, TW3=9, TW4=12

        // Ambil SP + IKP berdasarkan satker bidang
        $sasaranPrograms = SasaranProgram::where('id_satker', $idSatkerBidang)
            ->where('tahun', $tahun)
            ->with(['indikatorKinerjaPrograms' => function ($q) use ($idSatkerBidang, $tahun) {
                $q->where('id_satker', $idSatkerBidang)->where('tahun', $tahun);
            }])
            ->get();

        $data = [];
        foreach ($sasaranPrograms as $sp) {
            $spData = [
                'sp_id' => $sp->id,
                'kode_sp' => $sp->kode_sp,
                'nama_sp' => $sp->nama_sp,
                'ikps' => []
            ];

            foreach ($sp->indikatorKinerjaPrograms as $ikp) {
                // Ambil capaian dari tabel pengukuran (khusus = 2)
                $capaian = Pengukuran::where('indikator_id', $ikp->id)
                    ->where('id_satker', $idSatkerBidang)
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan_akhir)
                    ->where('khusus', 2)
                    ->value('capaian') ?? 0;

                // Ambil target
                $target = TargetIkp::where('ikp_id', $ikp->id)
                    ->where('id_satker', $idSatkerBidang)
                    ->where('tahun', $tahun)
                    ->value('target_tahun') ?? 0;

                // Hitung capaian PK
                $capaian_pk = $target > 0 ? round(($capaian / $target) * 100, 2) : 0;

                // Ambil faktor & langkah dari pelaporan_ikp
                $pelaporan = PelaporanIkp::where('ikp_id', $ikp->id)
                    ->where('id_satker', $idSatkerBidang)
                    ->where('tahun', $tahun)
                    ->where('triwulan', $tw)
                    ->first();

                $spData['ikps'][] = [
                    'ikp_id' => $ikp->id,
                    'kode_ikp' => $ikp->kode_ikp,
                    'nama_ikp' => $ikp->nama_ikp,
                    'persentase' => (float)$capaian,
                    'target_pk' => (float)$target,
                    'capaian_pk' => (float)$capaian_pk,
                    'faktor' => $pelaporan->faktor ?? '',
                    'langkah' => $pelaporan->langkah_optimalisasi ?? '',
                ];
            }
            $data[] = $spData;
        }

        return response()->json($data);
    }

    /**
     * Menyimpan faktor-faktor dan upaya optimalisasi per IKP
     */
    public function simpanIkpKeterangan(Request $request)
    {
        $request->validate([
            'ikp_id' => 'required|integer',
            'id_satker' => 'required',
            'faktor' => 'nullable|string',
            'langkah' => 'nullable|string',
            'triwulan' => 'required|integer|min:1|max:4',
        ]);

        $tahun = session('tahun_terpilih');
        $idSatker = $request->id_satker;

        PelaporanIkp::updateOrCreate(
            [
                'ikp_id' => $request->ikp_id,
                'id_satker' => $idSatker,
                'tahun' => $tahun,
                'triwulan' => $request->triwulan,
            ],
            [
                'faktor' => $request->faktor,
                'langkah_optimalisasi' => $request->langkah,
            ]
        );

        return response()->json(['status' => 'success', 'message' => 'Keterangan IKP berhasil disimpan!']);
    }
}
