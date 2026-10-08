<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\LheAkip;
use App\Models\MonevRenaksi;
use App\Models\TlLheAkip;
use App\Models\DataLke1;
use App\Models\LkeKomponen;
use App\Models\LkeBuktidukung;
use App\Helpers\LkeEvidenceHelper;

class EvaluasiController extends Controller
{
    public function index()
    {
        // Cek apakah tahun sudah dipilih
        if (!session()->has('tahun_terpilih')) {
            return redirect()->route('pilih.tahun');
        }
        // Ambil tahun yang dipilih dari session
        $tahun = session('tahun_terpilih');
        $idSatker = session('id_satker');

        $lheAkipFiles = LheAkip::orderBy('id_tglupload', 'desc')->get();
        $lheAkipFiles = LheAkip::where('id_satker', $idSatker)
            ->where('id_periode', $tahun)
            ->orderBy('id_tglupload', 'desc')
            ->get();
        $tlLheAkipFiles = TLLheAkip::where('id_satker', $idSatker)
            ->where('id_periode', $tahun)
            ->orderBy('id_tglupload', 'desc')
            ->get();
        $monevRenaksiFiles = MonevRenaksi::where('id_periode', $tahun)
            ->where('id_satker', $idSatker)
            ->orderBy('id_perubahan', 'desc')
            ->get();

        // === Ambil dokumen bukti dukung (group by kriteria) ===
        $buktiDukung = DB::table('bukti_dukung')
            ->where('id_satker', $idSatker)
            ->get()
            ->groupBy('id_kriteria');

        // === Ambil semua kriteria yang punya format_nama_file ===
        $kriteria = DB::table('kriteria')->get();

        foreach ($kriteria as $row) {
            if (!$row->format_nama_file) continue;

            $formats = array_map('trim', explode(';', $row->format_nama_file));

            foreach ($formats as $format) {
                $regex = '/^' . str_replace('_', '.*', preg_quote($format, '/')) . '/i';

                $dokumenList = DB::table('bukti_dukung')
                    ->where('id_satker', $idSatker)
                    ->where('id_kriteria', $row->id)
                    ->get()
                    ->filter(fn($d) => preg_match($regex, $d->link_bukti_dukung));

                if ($dokumenList->isNotEmpty()) {
                    if (!isset($buktiDukung[$row->id])) {
                        $buktiDukung[$row->id] = collect();
                    }
                    foreach ($dokumenList as $dok) {
                        $buktiDukung[$row->id]->push($dok);
                    }
                }
            }
        }

        $komponen = DB::table('komponen')
            ->leftJoin('sub_komponen', 'komponen.id', '=', 'sub_komponen.id_komponen')
            ->leftJoin('kriteria', 'sub_komponen.id_subkomponen', '=', 'kriteria.id_sub_komponen')
            ->select(
                'komponen.id as id_komponen',
                'komponen.nama_komponen',
                'sub_komponen.id_subkomponen',
                'sub_komponen.nama_subkomponen',
                'kriteria.id as id_kriteria',
                'kriteria.nama_kriteria',
                'kriteria.bukti_pengisian',
                'kriteria.format_nama_file',
                'kriteria.kode'
            )
            ->orderBy('komponen.id')
            ->orderBy('sub_komponen.id_subkomponen')
            ->orderBy('kriteria.id')
            ->get();


        // Tambahkan info jumlah kebutuhan vs realisasi
        $komponen->transform(function ($item) use ($buktiDukung) {
            // pecah kebutuhan dari kriteria
            $kebutuhan = $item->bukti_pengisian
                ? array_map('trim', explode(';', $item->bukti_pengisian))
                : [];

            // dokumen realisasi
            $realisasi = isset($buktiDukung[$item->id_kriteria])
                ? $buktiDukung[$item->id_kriteria]
                : collect();

            $item->jumlah_kebutuhan = count($kebutuhan);
            $item->jumlah_realisasi = $realisasi->count();
            $item->persen_terpenuhi = $item->jumlah_kebutuhan > 0
                ? round(($item->jumlah_realisasi / $item->jumlah_kebutuhan) * 100, 2)
                : 0;

            return $item;
        });

        // Load Unified LKE Hierarchy and Evidence for synchronized UX (matching Penilaian Mandiri)
        $targetTahun = LkeKomponen::where('tahun', $tahun)->exists() ? $tahun : 2025;
        $komponenList = LkeKomponen::where('tahun', $targetTahun)
            ->with([
                'subkomponen' => function ($q) use ($targetTahun) {
                    $q->where('tahun', $targetTahun);
                },
                'subkomponen.kriteria' => function ($q) use ($targetTahun) {
                    $q->where('tahun', $targetTahun);
                },
            ])
            ->orderBy('id')
            ->get();

        $satkerBukti = LkeEvidenceHelper::getUnifiedSatkerEvidences($idSatker, $tahun);
        $masterBukti = LkeBuktidukung::pluck('dokumen', 'id');

        // Mapping format file & kebutuhan kriteria berdasarkan kode kriteria bersih
        $legacyKriteria = DB::table('kriteria')->get();
        $formatMap = [];
        $needMap = [];
        foreach ($legacyKriteria as $lk) {
            $cleanCode = LkeEvidenceHelper::cleanKriteriaKode($lk->kode ?: $lk->id);
            if (!isset($formatMap[$cleanCode])) {
                $formatMap[$cleanCode] = [];
                $needMap[$cleanCode] = [];
            }
            if ($lk->format_nama_file) {
                foreach (explode(';', $lk->format_nama_file) as $f) {
                    $f = trim($f);
                    if ($f && !in_array($f, $formatMap[$cleanCode])) {
                        $formatMap[$cleanCode][] = $f;
                    }
                }
            }
            if ($lk->bukti_pengisian) {
                foreach (explode(';', $lk->bukti_pengisian) as $n) {
                    $n = trim($n);
                    if ($n && !in_array($n, $needMap[$cleanCode])) {
                        $needMap[$cleanCode][] = $n;
                    }
                }
            }
        }

        // Summary metrics kelengkapan bukti dukung LKE yang sinkron dengan Penilaian Mandiri
        $allCriteria = $komponenList->flatMap(fn($k) => $k->subkomponen->flatMap(fn($s) => $s->kriteria));
        $totalKriteriaLke = $allCriteria->count();
        $lengkapCount = 0;
        foreach ($allCriteria as $cr) {
            $reqIds = $cr->bukti_dukung_ids;
            if (empty($reqIds)) {
                $lengkapCount++;
                continue;
            }
            $crEvidences = $satkerBukti->get($cr->kode, collect());
            $allMet = true;
            foreach ($reqIds as $bId) {
                $has = $crEvidences->first(function ($item) use ($bId) {
                    return $item->buktidukung_id == $bId
                        || $item->kode_bukti == $bId
                        || LkeEvidenceHelper::matchesBuktiId($item->link_bukti_dukung, (int)$bId);
                });
                if (!$has) {
                    $allMet = false;
                    break;
                }
            }
            if ($allMet) {
                $lengkapCount++;
            }
        }
        $belumCount = $totalKriteriaLke - $lengkapCount;
        $persenLengkap = $totalKriteriaLke > 0 ? round(($lengkapCount / $totalKriteriaLke) * 100, 1) : 0;

        $sections = [
            'Perencanaan' => DataLke1::where('subkomponen_id', 'LIKE', '1.%')->get(),
            'Pengukuran'  => DataLke1::where('subkomponen_id', 'LIKE', '2.%')->get(),
            'Pelaporan'   => DataLke1::where('subkomponen_id', 'LIKE', '3.%')->get(),
            'Evaluasi Akuntabilitas Kinerja Internal (LKE Eval AKIP)' => DataLke1::where('subkomponen_id', 'LIKE', '4.%')->get(),
        ];

        return view('kelola.evaluasi', [
            'tahun'             => $tahun,
            'lheAkipFiles'      => $lheAkipFiles,
            'monevRenaksiFiles' => $monevRenaksiFiles,
            'tlLheAkipFiles'    => $tlLheAkipFiles,
            'komponen'          => $komponen,
            'komponenList'      => $komponenList,
            'satkerBukti'       => $satkerBukti,
            'masterBukti'       => $masterBukti,
            'formatMap'         => $formatMap,
            'needMap'           => $needMap,
            'idSatker'          => $idSatker,
            'buktiDukung'       => $buktiDukung,
            'sections'          => $sections,
            'totalKriteriaLke'  => $totalKriteriaLke,
            'lengkapCount'      => $lengkapCount,
            'belumCount'        => $belumCount,
            'persenLengkap'     => $persenLengkap,
        ]);
    }

    // 📌 Upload LHE AKIP
    public function uploadLheAkip(Request $request)
    {
        $request->validate([
            'lhe_akip_file' => 'required|mimes:pdf|max:4096',
        ]);
        $tahun = session('tahun_terpilih');
        $idSatker = session('id_satker');

        // Cek id_perubahan yang sudah ada
        $latestLhe = LheAkip::where('id_satker', $idSatker)
            ->where('id_periode', $tahun)
            ->orderBy(DB::raw('CAST(id_perubahan AS UNSIGNED)'), 'desc')
            ->first();

        // Tentukan id_perubahan
        $id_perubahan = $latestLhe ? $latestLhe->id_perubahan + 1 : 0;

        // Upload file ke folder public/uploads/lkjip
        $file = $request->file('lhe_akip_file');
        $fileName = 'lhe_' . $tahun . '_' . $id_perubahan . '.pdf';
        $file->move(base_path('uploads/repository/' . $idSatker), $fileName);

        LheAkip::create([
            'id_periode' => $tahun,
            'id_satker' => $idSatker,
            'id_perubahan' => $id_perubahan,
            'id_filename' => $fileName,
            'id_tglupload' => now()->format('d/m/Y h:i A'),

        ]);

        return redirect()->route('evaluasi')->with(['success-lhe' => 'LHE AKIP berhasil diupload!', 'active_tab' => 'lhe-akip']);
    }

    public function uploadTlLheAkip(Request $request)
    {
        $request->validate([
            'tllhe_file' => 'required|mimes:pdf|max:4096',
        ]);

        $tahun = session('tahun_terpilih');
        $idSatker = session('id_satker');

        // Cek versi terbaru
        $latestFile = TlLheAkip::where('id_satker', $idSatker)
            ->where('id_periode', $tahun)
            ->orderBy(DB::raw('CAST(id_perubahan AS UNSIGNED)'), 'desc')
            ->first();

        $id_perubahan = $latestFile ? $latestFile->id_perubahan + 1 : 0;

        if ($request->hasFile('tllhe_file')) {
            $file = $request->file('tllhe_file');
            $filename = 'tllhe_' . $tahun . '_' . $id_perubahan . '.pdf';
            $file->move(public_path('uploads/repository/' . $idSatker), $filename);

            TlLheAkip::create([
                'id_periode' => $tahun,
                'id_satker' => $idSatker,
                'id_perubahan' => $id_perubahan,
                'id_filename' => $filename,
                'id_tglupload' => now()->format('d/m/Y h:i A'),

            ]);

            return redirect()->route('evaluasi')->with(['success-tllhe' => 'File TL LHE AKIP berhasil diunggah.', 'active_tab' => 'tl-lhe-akip']);
        }

        return back()->with('error', 'Gagal mengunggah file.');
    }

    public function uploadMonevRenaksi(Request $request)
    {
        $request->validate([
            'id_triwulan' => 'required|in:TW 1,TW 2,TW 3,TW 4',
            'monev_file' => 'required|mimes:pdf|max:4096',
        ]);

        $tahun = session('tahun_terpilih');
        $idSatker = session('id_satker');
        $id_triwulan = $request->input('id_triwulan');

        // Ambil versi terakhir untuk triwulan tersebut
        $latestmonev = MonevRenaksi::where('id_satker', $idSatker)
            ->where('id_periode', $tahun)
            ->where('id_triwulan', $id_triwulan)
            ->orderBy(DB::raw('CAST(id_perubahan AS UNSIGNED)'), 'desc')
            ->first();

        $id_perubahan = $latestmonev ? $latestmonev->id_perubahan + 1 : 0;

        if ($request->hasFile('monev_file')) {
            $file = $request->file('monev_file');

            // Pastikan folder ada
            $destination = base_path('uploads/repository/' . $idSatker);
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            $filename = 'renaksieval_' . $tahun . '_' . $id_perubahan . '_' . $id_triwulan . '.pdf';
            $file->move($destination, $filename);

            // Simpan data ke database
            MonevRenaksi::create([
                'id_periode' => $tahun,
                'id_satker' => $idSatker,
                'id_perubahan' => $id_perubahan,
                'id_filename' => $filename,
                'id_tglupload' => now()->format('d/m/Y h:i A'),
                'id_triwulan' => $id_triwulan,
            ]);

            return redirect()->route('evaluasi')
                ->with([
                    'success-monev' => 'File Monev Renaksi berhasil diunggah.',
                    'active_tab' => 'monev-renaksi'
                ]);
        }

        return back()->with('error', 'Gagal mengunggah file.');
    }


    public function verifikasi(Request $request)
    {
        $idKriteria    = $request->input('id_kriteria');
        $idSatker      = session('id_satker');
        $idKomponen    = $request->input('id_komponen');
        $idSubKomponen = $request->input('id_subkomponen');
        $tahun         = session('tahun_terpilih', date('Y'));

        // Ambil formats jika dikirimkan langsung
        $formats = [];
        if ($request->has('formats') && !empty($request->input('formats'))) {
            $rawFormats = $request->input('formats');
            $formats = is_array($rawFormats) ? $rawFormats : json_decode($rawFormats, true);
        }

        // Jika formats kosong, cari dari tabel kriteria (support id, kode, atau prefix)
        if (empty($formats)) {
            $matchingKriteria = DB::table('kriteria')
                ->where('id', $idKriteria)
                ->orWhere('kode', $idKriteria)
                ->orWhere('id', 'like', $idKriteria . '.%')
                ->get();

            foreach ($matchingKriteria as $mk) {
                if ($mk->format_nama_file) {
                    foreach (explode(';', $mk->format_nama_file) as $f) {
                        $f = trim($f);
                        if ($f && !in_array($f, $formats)) {
                            $formats[] = $f;
                        }
                    }
                }
            }
        }

        if (empty($formats)) {
            return response()->json(['success' => false, 'message' => 'Format dokumen kriteria tidak ditemukan.']);
        }

        $mapTable = [
            'renstra'     => ['table' => 'sinori_sakip_renstra',    'prefix' => 'renstra'],
            'renja'       => ['table' => 'sinori_sakip_renja',      'prefix' => 'renja'],
            'iku'         => ['table' => 'sinori_sakip_iku',        'prefix' => 'IKU'],
            'pk'          => ['table' => 'pk',                      'prefix' => 'pk'],
            'renaksi'     => ['table' => 'sinori_sakip_renaksi',    'prefix' => 'renaksi'],
            'rkakl'       => ['table' => 'sinori_sakip_rkakl',      'prefix' => 'rkakl'],
            'dipa'        => ['table' => 'sinori_sakip_dipa',       'prefix' => 'dipa'],
            'lkjip'       => ['table' => 'sinori_sakip_lakip',      'prefix' => 'lkjip'],
            'rastaff'     => ['table' => 'sinori_sakip_rastaff',    'prefix' => 'rastaff'],
            'renaksieval' => ['table' => 'sinori_sakip_renaksieval', 'prefix' => 'renaksieval'],
            'lhe'         => ['table' => 'lhe',                     'prefix' => 'lhe'],
            'tllhe'       => ['table' => 'tl_lhe_akip',             'prefix' => 'tllhe'],
        ];

        $results = [];

        foreach ($formats as $format) {
            if (stripos($format, '(perubahan terakhir)') !== false) {
                $tipe = strtolower(strtok($format, '_'));
                if (!isset($mapTable[$tipe])) continue;

                $source = $mapTable[$tipe];

                preg_match('/\d{4}/', $format, $match);
                $tahunFormat = $match[0] ?? $tahun;

                preg_match('/TW\s*[1-4]/i', $format, $matchPeriode);
                $periodeTambahan = $matchPeriode[0] ?? null;
                $idTriwulan = $matchPeriode[1] ?? null;

                $idPeriode = ($tipe === 'renstra')
                    ? (($tahunFormat == 2024) ? 'P1' : 'P2')
                    : $tahunFormat;

                $query = DB::table($source['table'])
                    ->where('id_satker', $idSatker)
                    ->where('id_periode', $idPeriode);

                if (in_array($source['table'], ['lakip', 'restaff', 'renaksieval', 'sinori_sakip_lakip', 'sinori_sakip_rastaff', 'sinori_sakip_renaksieval']) && $idTriwulan) {
                    $query->where('id_triwulan', $idTriwulan);
                }

                $dokumen = $query->orderByDesc('id_perubahan')->first();

                if ($dokumen) {
                    $namaFile = $source['prefix'] . '_' . $tahunFormat . '_' . $dokumen->id_perubahan;

                    if ($periodeTambahan) {
                        $namaFile .= '_' . strtoupper(str_replace('TW', 'TW', $periodeTambahan));
                    }
                    $namaFile .= '.pdf';

                    // Simpan tersinkronisasi ke bukti_dukung dan lke_satker_bukti
                    LkeEvidenceHelper::syncEvidenceRecord(
                        (string)$idSatker,
                        (string)$idKriteria,
                        $namaFile,
                        $idKomponen,
                        $idSubKomponen,
                        $tahun
                    );

                    $results[] = $namaFile;
                }
            }
        }
        return response()->json(['success' => true, 'synced' => $results]);
    }


    /**
     * Upload manual (untuk format tanpa "(perubahan terakhir)")
     */
    public function upload(Request $request)
    {
        $request->validate([
            'files.*'        => 'required|mimes:pdf|max:4096',
            'id_kriteria'    => 'required',
            'id_komponen'    => 'required',
            'id_subkomponen' => 'required',
        ]);

        $idKriteria    = $request->input('id_kriteria');
        $idSatker      = session('id_satker');
        $idKomponen    = $request->input('id_komponen');
        $idSubKomponen = $request->input('id_subkomponen');
        $tahun         = session('tahun_terpilih', date('Y'));

        $matchingKriteria = DB::table('kriteria')
            ->where('id', $idKriteria)
            ->orWhere('kode', $idKriteria)
            ->orWhere('id', 'like', $idKriteria . '.%')
            ->get();

        $formats = [];
        foreach ($matchingKriteria as $mk) {
            if ($mk->format_nama_file) {
                foreach (explode(';', $mk->format_nama_file) as $f) {
                    $f = trim($f);
                    if ($f && !in_array($f, $formats)) {
                        $formats[] = $f;
                    }
                }
            }
        }

        $manualFormats = collect($formats)->filter(fn($f) => stripos($f, '(perubahan terakhir)') === false)->values();

        $tujuanPath = public_path("uploads/repository/{$idSatker}");
        if (!file_exists($tujuanPath)) {
            mkdir($tujuanPath, 0777, true);
        }

        $uploadedFiles = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $index => $file) {
                $manualFormat = $manualFormats[$index] ?? ('dokumen_' . ($index + 1));
                $namaFile = str_replace(' ', '_', $manualFormat) . '.pdf';

                $file->move($tujuanPath, $namaFile);

                // Simpan tersinkronisasi ke bukti_dukung dan lke_satker_bukti
                LkeEvidenceHelper::syncEvidenceRecord(
                    (string)$idSatker,
                    (string)$idKriteria,
                    $namaFile,
                    $idKomponen,
                    $idSubKomponen,
                    $tahun
                );

                $uploadedFiles[] = asset("uploads/repository/{$idSatker}/{$namaFile}");
            }
        }

        return back()->with('success', 'Upload bukti dukung berhasil dan disinkronkan.');
    }
}
