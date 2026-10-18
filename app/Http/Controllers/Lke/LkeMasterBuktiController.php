<?php

namespace App\Http\Controllers\Lke;

use App\Http\Controllers\Controller;
use App\Models\LkeBuktidukung;
use Illuminate\Http\Request;

class LkeMasterBuktiController extends Controller
{
    /**
     * Check if current session user is Admin.
     */
    protected function checkAdmin()
    {
        $satker = session('id_satker');
        $level = session('id_sakip_level');
        $nama = strtolower(session('satkernama', ''));

        $isAdmin = in_array($satker, ['admin', '999999', '888881', 'Pengawasan', 'Panev'])
            || $level == '99'
            || str_contains(strtolower((string)$satker), 'admin')
            || str_contains($nama, 'admin')
            ;

        if (!$isAdmin) {
            abort(403, 'Akses Ditolak: Hanya Administrator yang dapat mengubah Master Bukti Dukung.');
        }
    }

    public function index(Request $request)
    {
        $this->checkAdmin();

        $masterBukti = LkeBuktidukung::orderBy('id')->get();
        
        // Get distinct tabel_sumber from DB (defensive check in case column is not yet migrated)
        $tabelSumberList = [];
        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn('lke_buktidukung', 'tabel_sumber')) {
                $tabelSumberList = LkeBuktidukung::select('tabel_sumber')
                    ->whereNotNull('tabel_sumber')
                    ->where('tabel_sumber', '!=', '')
                    ->distinct()
                    ->pluck('tabel_sumber')
                    ->toArray();
            }
        } catch (\Throwable $e) {
            $tabelSumberList = [];
        }

        // Ensure these common tables are at least in the list even if not in DB yet
        $commonTables = [
            'sinori_sakip_renja', 'sinori_sakip_renaksi', 'sinori_sakip_renstra',
            'sinori_sakip_rkakl', 'sinori_sakip_iku', 'sinori_sakip_lakip',
            'sinori_sakip_rastaff', 'sinori_sakip_dipa', 'sinori_sakip_renaksieval'
        ];
        $tabelSumberList = array_unique(array_merge($tabelSumberList, $commonTables));
        sort($tabelSumberList);

        // Generate years list
        $currentYear = (int)date('Y');
        $tahunList = range(2023, $currentYear + 2);

        return view('lke.master_bukti.index', compact('masterBukti', 'tabelSumberList', 'tahunList'));
    }

    public function store(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'dokumen' => 'required|string|max:500',
            'tabel_sumber' => 'nullable|string|max:100',
            'format_nama_file' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'tahun' => 'nullable|integer',
        ]);

        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', trim($request->input('dokumen'))));
        $format = $request->input('format_nama_file');
        if (empty($format)) {
            $format = $slug . '_{tahun}.pdf';
        }

        $bukti = new LkeBuktidukung();
        $bukti->dokumen = $request->input('dokumen');
        $bukti->tabel_sumber = $request->input('tabel_sumber') ?: null;
        $bukti->format_nama_file = $format;
        $bukti->keterangan = $request->input('keterangan') ?: '-';
        $bukti->ada_di_sistem = !empty($request->input('tabel_sumber')) ? 1 : 0;
        $bukti->tahun = $request->input('tahun') ?: null;
        $bukti->save();
        \App\Helpers\LkeEvidenceHelper::clearCache();

        return redirect()->route('lke.master_bukti.index')->with('success', 'Bukti Dukung Baru [ID #' . $bukti->id . '] berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $this->checkAdmin();

        $request->validate([
            'dokumen' => 'required|string|max:500',
            'tabel_sumber' => 'nullable|string|max:100',
            'format_nama_file' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'tahun' => 'nullable|integer',
        ]);

        $bukti = LkeBuktidukung::findOrFail($id);
        $bukti->dokumen = $request->input('dokumen');
        $bukti->tabel_sumber = $request->input('tabel_sumber') ?: null;
        if ($request->has('format_nama_file')) {
            $bukti->format_nama_file = $request->input('format_nama_file');
        }
        if ($request->has('keterangan')) {
            $bukti->keterangan = $request->input('keterangan') ?: '-';
        }
        $bukti->ada_di_sistem = !empty($request->input('tabel_sumber')) ? 1 : 0;
        $bukti->tahun = $request->input('tahun') ?: null;
        $bukti->save();
        \App\Helpers\LkeEvidenceHelper::clearCache();

        return redirect()->route('lke.master_bukti.index')->with('success', 'Master Bukti Dukung [ID #' . $id . '] berhasil diperbarui!');
    }

    public function destroy(Request $request, $id)
    {
        $this->checkAdmin();

        $bukti = LkeBuktidukung::findOrFail($id);

        // Check if used in lke_gabungan
        $isUsedInGabungan = \App\Models\LkeGabungan::where('buktidukung_id', $id)->exists();
        // Check if used in lke_satker_bukti
        $isUsedInSatker = \App\Models\LkeSatkerBukti::where('buktidukung_id', $id)->exists();
        // Check if used in lke_kriteria (comma-separated kode_bukti)
        $isUsedInKriteria = \App\Models\LkeKriteria::whereRaw("FIND_IN_SET(?, REPLACE(kode_bukti, ' ', ''))", [$id])->exists();

        if ($isUsedInGabungan || $isUsedInSatker || $isUsedInKriteria) {
            return redirect()->route('lke.master_bukti.index')->withErrors([
                'error' => "Bukti Dukung ID #{$id} tidak dapat dihapus karena sedang digunakan dalam pemetaan kriteria atau bukti satuan kerja."
            ]);
        }

        $bukti->delete();
        \App\Helpers\LkeEvidenceHelper::clearCache();
        return redirect()->route('lke.master_bukti.index')->with('success', "Master Bukti Dukung ID #{$id} berhasil dihapus.");
    }

    /**
     * Sinkronisasi struktur tabel dan penambahan kolom berdasarkan rujukan simemek3.sql secara idempoten.
     */
    public function syncSchema(\App\Services\DatabaseSchemaSyncService $syncService)
    {
        $this->checkAdmin();

        try {
            $result = $syncService->sync();
            $msg = "Sinkronisasi struktur kolom database berhasil! " . ($result['added_columns'] > 0 
                ? "Berhasil menambahkan {$result['added_columns']} kolom baru dan {$result['added_tables']} tabel baru." 
                : "Semua kolom sudah ada dan lengkap ({$result['existing_columns']} kolom eksisting tidak perlu ditambah).");

            return redirect()->route('lke.master_bukti.index')->with('success', $msg);
        } catch (\Throwable $e) {
            return redirect()->route('lke.master_bukti.index')->withErrors([
                'error' => 'Gagal melakukan sinkronisasi kolom: ' . $e->getMessage()
            ]);
        }
    }
}
