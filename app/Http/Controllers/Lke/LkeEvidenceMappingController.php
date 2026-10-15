<?php

namespace App\Http\Controllers\Lke;

use App\Http\Controllers\Controller;
use App\Models\LkeBuktidukung;
use App\Models\LkeGabungan;
use App\Models\LkeKomponen;
use App\Models\LkeKriteria;
use App\Models\LkeSubkomponen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LkeEvidenceMappingController extends Controller
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
            || app()->environment('local'); // Allow in local testing if needed

        if (!$isAdmin) {
            abort(403, 'Akses Ditolak: Hanya Administrator yang dapat mengubah mapping Bukti Dukung.');
        }
    }

    /**
     * Display the Evidence Mapping Editor page.
     */
    public function index(Request $request)
    {
        $this->checkAdmin();

        $tahun = session('tahun_terpilih') ?? date('Y');
        $komponenFilter = $request->get('komponen_id');
        $subkomponenFilter = $request->get('subkomponen_id');
        $search = $request->get('search');

        // Komponen list for filter
        $komponenList = LkeKomponen::where('tahun', $tahun)
            ->orWhere('tahun', 2025)
            ->orderBy('id')
            ->get();

        // Subkomponen list
        $subkomponenList = LkeSubkomponen::where('tahun', $tahun)
            ->orWhere('tahun', 2025)
            ->orderBy('id')
            ->get();

        // Query Kriteria
        $kriteriaQuery = LkeKriteria::with(['subkomponen.komponen'])
            ->where(function ($q) use ($tahun) {
                $q->where('tahun', $tahun)->orWhere('tahun', 2025);
            });

        if ($subkomponenFilter) {
            $kriteriaQuery->where('subkomponen_id', $subkomponenFilter);
        } elseif ($komponenFilter) {
            $subIds = LkeSubkomponen::where('komponen_id', $komponenFilter)->pluck('kode');
            $kriteriaQuery->whereIn('subkomponen_id', $subIds);
        }

        if ($search) {
            $kriteriaQuery->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('dokumen_bukti', 'like', "%{$search}%");
            });
        }

        $kriteriaList = $kriteriaQuery->orderBy('id')->get();

        // Master Evidence list
        $masterBukti = LkeBuktidukung::orderBy('id')->get();

        // Mapping summary count
        $totalKriteria = LkeKriteria::count();
        $mappedCount = LkeKriteria::whereNotNull('kode_bukti')->where('kode_bukti', '!=', '')->count();

        return view('lke.evidence_mapping.index', compact(
            'kriteriaList',
            'komponenList',
            'subkomponenList',
            'masterBukti',
            'komponenFilter',
            'subkomponenFilter',
            'search',
            'tahun',
            'totalKriteria',
            'mappedCount'
        ));
    }

    /**
     * Get criteria details with currently mapped evidence for modal editor.
     */
    public function getCriteriaDetail($kode)
    {
        $this->checkAdmin();

        $tahun = session('tahun_terpilih') ?? date('Y');
        $kriteria = LkeKriteria::where('kode', $kode)
            ->where(function ($q) use ($tahun) {
                $q->where('tahun', $tahun)->orWhere('tahun', 2025);
            })
            ->orderBy('tahun', 'desc')
            ->firstOrFail();
        $selectedIds = $kriteria->bukti_dukung_ids;

        // Current mapped items
        $mappedItems = LkeBuktidukung::whereIn('id', $selectedIds)->get();

        // All available items with checked flag
        $allBukti = LkeBuktidukung::orderBy('id')->get()->map(function ($b) use ($selectedIds) {
            $b->is_selected = in_array($b->id, $selectedIds);
            return $b;
        });

        return response()->json([
            'success'     => true,
            'kriteria'    => $kriteria,
            'selectedIds' => $selectedIds,
            'mappedItems' => $mappedItems,
            'allBukti'    => $allBukti,
        ]);
    }

    /**
     * Update the evidence mapping for a criteria.
     */
    public function updateMapping(Request $request, $kode)
    {
        $this->checkAdmin();

        $request->validate([
            'bukti_ids'     => 'nullable|array',
            'bukti_ids.*'   => 'integer|exists:lke_buktidukung,id',
            'langkah_kerja' => 'nullable|string',
        ]);

        $tahun = session('tahun_terpilih') ?? date('Y');
        $kriteria = LkeKriteria::where('kode', $kode)
            ->where(function ($q) use ($tahun) {
                $q->where('tahun', $tahun)->orWhere('tahun', 2025);
            })
            ->orderBy('tahun', 'desc')
            ->firstOrFail();
        $buktiIds = $request->input('bukti_ids', []);

        // Sort IDs numerically
        sort($buktiIds);

        // Fetch the corresponding master documents to update dokumen_bukti text
        $docs = LkeBuktidukung::whereIn('id', $buktiIds)->orderBy('id')->get();

        // Build new text description for dokumen_bukti
        $docDescriptions = [];
        foreach ($docs as $idx => $d) {
            $num = $idx + 1;
            $docDescriptions[] = "{$num}. {$d->dokumen}";
        }
        $dokumenBuktiText = implode("\n", $docDescriptions);

        DB::beginTransaction();
        try {
            // 1. Update lke_kriteria
            $kriteria->kode_bukti = !empty($buktiIds) ? implode(', ', $buktiIds) : null;
            if (!empty($dokumenBuktiText)) {
                $kriteria->dokumen_bukti = $dokumenBuktiText;
            }
            if ($request->has('langkah_kerja')) {
                $kriteria->langkah_kerja = $request->input('langkah_kerja');
            }
            $kriteria->save();
            $nowYear = $kriteria->tahun ?? (session('tahun_terpilih') ?? 2025);

            // 2. Synchronize lke_gabungan
            // Remove existing gabungan for this kriteria in current year
            LkeGabungan::where('kriteria_id', $kode)->where('tahun', $nowYear)->delete();

            // Determine komponen_id and sub_komponen_id
            $sub = LkeSubkomponen::where('kode', $kriteria->subkomponen_id)->first();
            $komponenId = $sub ? $sub->komponen_id : 1;

            $insertGabungan = [];
            foreach ($buktiIds as $bId) {
                $insertGabungan[] = [
                    'komponen_id'     => $komponenId,
                    'sub_komponen_id' => $kriteria->subkomponen_id,
                    'kriteria_id'     => $kriteria->kode,
                    'buktidukung_id'  => $bId,
                    'tahun'           => $nowYear,
                ];
            }

            if (!empty($insertGabungan)) {
                LkeGabungan::insert($insertGabungan);
            }

            DB::commit();

            return response()->json([
                'success'       => true,
                'message'       => "Mapping Bukti Dukung untuk Kriteria {$kode} berhasil diperbarui!",
                'kode_bukti'    => $kriteria->kode_bukti,
                'dokumen_bukti' => $kriteria->dokumen_bukti,
                'langkah_kerja' => $kriteria->langkah_kerja,
                'mapped_items'  => $docs,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui mapping: ' . $e->getMessage(),
            ], 500);
        }
    }
}
