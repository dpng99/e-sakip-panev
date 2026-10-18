# Panduan Eksekusi Pengembangan PROSAKIP Kejaksaan RI (`laravel11`)

Dokumen ini menyimpan seluruh cetak biru, *diff*, dan potongan kode siap pakai dari hasil analisis menyeluruh codebase **PROSAKIP Kejaksaan RI**. Seluruh file kode aplikasi saat ini masih berada dalam kondisi asli (*belum dieksekusi*), sehingga Anda dapat mengeksekusi pengembangan ini per tahap atau sekaligus kapan saja.

> [!TIP]
> **Cara Cepat Mengeksekusi dengan AI Assistant:**
> Cukup ketik perintah seperti:
> - *"Eksekusi Tahap 1 dari `PANDUAN_EKSEKUSI_PENGEMBANGAN.md`"*
> - *"Eksekusi seluruh tahapan di `PANDUAN_EKSEKUSI_PENGEMBANGAN.md`"*

---

## Daftar Isi Tahapan Pengembangan
1. **[Tahap 1 — Perbaikan Bug Kritis & Route Rusak (Kategori A)](#tahap-1--perbaikan-bug-kritis--route-rusak-kategori-a)**
2. **[Tahap 2 — Fitur Rekapitulasi Peringkat & Export Excel Kertas Kerja LKE AKIP (Kategori B1)](#tahap-2--fitur-rekapitulasi-peringkat--export-excel-kertas-kerja-lke-akip-kategori-b1)**
3. **[Tahap 3 — Kelengkapan Seeder Cascading 2026 Seluruh 9 Unit Eselon I termasuk JAMBIN (Kategori B2)](#tahap-3--kelengkapan-seeder-cascading-2026-seluruh-9-unit-eselon-i-termasuk-jambin-kategori-b2)**
4. **[Tahap 4 — Optimasi Performa Query, Keamanan & Pembersihan File Duplikat (Kategori C)](#tahap-4--optimasi-performa-query-keamanan--pembersihan-file-duplikat-kategori-c)**

---

## Tahap 1 — Perbaikan Bug Kritis & Route Rusak (Kategori A)

### 1.1. Perbaikan Route Logout SPIP, Registrasi Route Pengumuman & Aturan CUD, serta Proteksi Admin LKE
- **File Target**: [`routes/web.php`](file:///c:/Users/Sicana%20350/Documents/Project/laravel11/routes/web.php)
- **Masalah**:
  1. `Route::post('/logout', [SpipAuthController::class, 'logout'])` di baris 183 bentrok dengan `Route::post('logout', [LoginController::class, 'logout'])` di baris 186.
  2. Halaman `/pengumuman` dan `/aturan` memanggil `route('pengumuman.store')`, `route('pengumuman.edit')`, `route('pengumuman.update')`, `route('pengumuman.destroy')`, dan `route('aturan.create')` yang belum terdaftar di `routes/web.php`.
  3. Route `/lke/evidence-mapping*` dan `/lke/master-bukti*` berada di luar grup middleware `admin`.

**Perubahan pada `routes/web.php`:**
```diff
@@ -183,7 +183,7 @@
-Route::post('/logout', [SpipAuthController::class, 'logout']);
+Route::post('/logout-spip', [SpipAuthController::class, 'logout'])->name('logout.spip');

 // Handle Logout
 Route::post('logout', [LoginController::class, 'logout'])->name('logout');
@@ -280,19 +280,10 @@
     // === Sistem LKE Baru (Evaluasi Pengawasan & Evidence Mapping) ===
     Route::get('/lke/evaluasi', [LkeEvaluasiController::class, 'index'])->name('lke.evaluasi.index');
+    Route::get('/lke/evaluasi/rekap-ranking', [LkeEvaluasiController::class, 'getRekapRanking'])->name('lke.evaluasi.rekap_ranking');
     Route::post('/lke/evaluasi/save-score', [LkeEvaluasiController::class, 'saveScore'])->name('lke.evaluasi.save_score');
     Route::post('/lke/evaluasi/save-koefisien', [LkeEvaluasiController::class, 'saveSubkomponenKoefisien'])->name('lke.evaluasi.save_koefisien');

-    // Evidence Mapping & Master Bukti Dukung
-    Route::get('/lke/evidence-mapping', [LkeEvidenceMappingController::class, 'index'])->name('lke.evidence_mapping.index');
-    Route::get('/lke/evidence-mapping/{kode}', [LkeEvidenceMappingController::class, 'getCriteriaDetail'])->name('lke.evidence_mapping.detail');
-    Route::post('/lke/evidence-mapping/{kode}', [LkeEvidenceMappingController::class, 'updateMapping'])->name('lke.evidence_mapping.update');
-
-    Route::get('/lke/master-bukti', [LkeMasterBuktiController::class, 'index'])->name('lke.master_bukti.index');
-    Route::post('/lke/master-bukti', [LkeMasterBuktiController::class, 'store'])->name('lke.master_bukti.store');
-    Route::put('/lke/master-bukti/{id}', [LkeMasterBuktiController::class, 'update'])->name('lke.master_bukti.update');
-    Route::delete('/lke/master-bukti/{id}', [LkeMasterBuktiController::class, 'destroy'])->name('lke.master_bukti.destroy');
-
     // === Pengumuman ===
     Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman');
@@ -346,11 +337,25 @@
         // === Pengumuman CUD ===
+        Route::post('/pengumuman', [PengumumanController::class, 'store'])->name('pengumuman.store');
+        Route::get('/pengumuman/{id}/edit', [PengumumanController::class, 'edit'])->name('pengumuman.edit');
+        Route::put('/pengumuman/{id}', [PengumumanController::class, 'update'])->name('pengumuman.update');
+        Route::delete('/pengumuman/{id}', [PengumumanController::class, 'destroy'])->name('pengumuman.destroy');

         // === Aturan CUD ===
+        Route::get('/aturan/create', [AturanController::class, 'create'])->name('aturan.create');
         Route::post('/aturan', [AturanController::class, 'store'])->name('aturan.store');
         Route::get('/aturan/{id}/edit', [AturanController::class, 'edit'])->name('aturan.edit');
         Route::put('/aturan/{id}', [AturanController::class, 'update'])->name('aturan.update');
         Route::delete('/aturan/{id}', [AturanController::class, 'destroy'])->name('aturan.destroy');

         // === LKE Master Bukti & Evidence Mapping ===
+        Route::get('/lke/evidence-mapping', [LkeEvidenceMappingController::class, 'index'])->name('lke.evidence_mapping.index');
+        Route::get('/lke/evidence-mapping/{kode}', [LkeEvidenceMappingController::class, 'getCriteriaDetail'])->name('lke.evidence_mapping.detail');
+        Route::post('/lke/evidence-mapping/{kode}', [LkeEvidenceMappingController::class, 'updateMapping'])->name('lke.evidence_mapping.update');
+
+        Route::get('/lke/master-bukti', [LkeMasterBuktiController::class, 'index'])->name('lke.master_bukti.index');
+        Route::post('/lke/master-bukti', [LkeMasterBuktiController::class, 'store'])->name('lke.master_bukti.store');
+        Route::put('/lke/master-bukti/{id}', [LkeMasterBuktiController::class, 'update'])->name('lke.master_bukti.update');
+        Route::delete('/lke/master-bukti/{id}', [LkeMasterBuktiController::class, 'destroy'])->name('lke.master_bukti.destroy');
     });
```

---

### 1.2. Perbaikan Path Upload Monev Renaksi (Agar Tidak 404)
- **File Target**: [`app/Http/Controllers/EvaluasiController.php`](file:///c:/Users/Sicana%20350/Documents/Project/laravel11/app/Http/Controllers/EvaluasiController.php#L637-L645)
- **Masalah**: Method `uploadMonevRenaksi()` menyimpan file ke `base_path('uploads/repository/' . $idSatker)` alih-alih `public_path('uploads/repository/' . $idSatker)`.

```diff
@@ -637,5 +637,5 @@
-            // Pastikan folder ada
-            $destination = base_path('uploads/repository/' . $idSatker);
+            // Pastikan folder ada di public/uploads/repository/{id_satker}
+            $destination = public_path('uploads/repository/' . $idSatker);
             if (!file_exists($destination)) {
                 mkdir($destination, 0755, true);
             }
```

---

### 1.3. Perbaikan Rumus Indikator `UNFAVORABLE` & Penambahan `JAMBIN (005016)` pada Pengukuran IKP
- **File Target**: [`app/Http/Controllers/PengukuranIkpController.php`](file:///c:/Users/Sicana%20350/Documents/Project/laravel11/app/Http/Controllers/PengukuranIkpController.php#L33-L250)

```diff
@@ -34,6 +34,11 @@
     {
         $all = [
             [
+                'id' => '005016',
+                'nama' => 'Jaksa Agung Muda Bidang Pembinaan',
+                'alias' => ['pembinaan', 'jambin']
+            ],
+            [
                 'id' => '691270',
                 'nama' => 'Badan Pemulihan Aset',
@@ -243,3 +248,11 @@
-                    $perhitungan = ($targetTwVal > 0) ? round(($floatVal / $targetTwVal) * 100, 2) : 0;
+                    if ($ikp->sifat_node === 'UNFAVORABLE') {
+                        $perhitungan = ($targetTwVal > 0 && $floatVal > 0)
+                            ? round(($targetTwVal / $floatVal) * 100, 2)
+                            : 0;
+                    } else {
+                        $perhitungan = ($targetTwVal > 0)
+                            ? round(($floatVal / $targetTwVal) * 100, 2)
+                            : 0;
+                    }
```

---

### 1.4. Pengaktifan Kembali `deleteLkjip` & Dukungan `UNFAVORABLE` pada Pelaporan IKP
- **File Target**: [`app/Http/Controllers/PelaporanController.php`](file:///c:/Users/Sicana%20350/Documents/Project/laravel11/app/Http/Controllers/PelaporanController.php#L372-L490)

```php
    public function deleteLkjip($id)
    {
        $idSatker = (string) session('id_satker');
        $level = session('id_sakip_level');
        $file = Lkjip::findOrFail($id);

        $isAdmin = in_array($idSatker, ['admin', '999999', '888881']) || $level == 99 || $level == '99';
        if (!$isAdmin && (string) $file->id_satker !== $idSatker) {
            abort(403, 'Akses ditolak.');
        }

        $filename = $file->id_filename ?? $file->filename;
        if ($filename) {
            $filePath = public_path('uploads/repository/' . $file->id_satker . '/' . $filename);
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        $file->delete();

        return redirect()->back()->with(['success-lkjip' => 'File LKJiP berhasil dihapus.', 'active_tab' => 'lkjip']);
    }
```

Dan pada `PelaporanController::getIkpData()` (baris 465–488):
```php
                $targetRecord = TargetIkp::where('ikp_id', $ikp->id)
                    ->where('id_satker', $idSatkerBidang)
                    ->where('tahun', $tahun)
                    ->first();

                $targetField = 'target_tw' . $tw;
                $target = ($targetRecord && $targetRecord->$targetField !== null)
                    ? (float)$targetRecord->$targetField
                    : (float)($targetRecord->target_tahun ?? 0);

                if ($ikp->sifat_node === 'UNFAVORABLE') {
                    $capaian_pk = ($target > 0 && $capaian > 0) ? round(($target / $capaian) * 100, 2) : 0;
                } else {
                    $capaian_pk = $target > 0 ? round(($capaian / $target) * 100, 2) : 0;
                }
```

---

## Tahap 2 — Fitur Rekapitulasi Peringkat & Export Excel Kertas Kerja LKE AKIP (Kategori B1)

### 2.1. Endpoint `getRekapRanking()` Tanpa N+1 Query
- **File Target**: [`app/Http/Controllers/Lke/LkeEvaluasiController.php`](file:///c:/Users/Sicana%20350/Documents/Project/laravel11/app/Http/Controllers/Lke/LkeEvaluasiController.php)
- Tambahkan method `getRekapRanking(Request $request)` di akhir class `LkeEvaluasiController` yang melakukan *preload* `LkePenilaianSatker` dan `LkeSubkomponenKoefisien` dalam 2 query teragregasi (`groupBy('id_satker')`), lalu menghitung skor PM, skor Evaluasi, selisih, dan Predikat SAKIP (`AA` s.d. `D`) untuk seluruh satker dalam yurisdiksi evaluator aktif (`Admin`, `JAMWAS 888882`, atau `Pengawasan Kejati wasxxxxxx`).

```php
    public function getRekapRanking(Request $request)
    {
        $this->checkEvaluator();

        $tahun = (int)$request->get('tahun', session('tahun_terpilih') ?? 2025);
        $evaluatorType = $this->getEvaluatorType();
        $currentUser = $this->getCurrentUser();
        $userSatkerId = (string)($currentUser ? $currentUser->id_satker : session('id_satker'));

        if ($evaluatorType === 'jamwas') {
            $satkerList = DB::table('sinori_login')
                ->where(function ($q) {
                    $q->where('id_sakip_level', 2)
                      ->orWhere(function ($sub) {
                          $sub->where('id_sakip_level', 1)
                              ->whereIn('id_satker', $this->centralUnitSatkerIds);
                      });
                })
                ->where('id_satker', 'not like', 'was%')
                ->whereNotIn('id_satker', ['admin', '999999', '888881', '888882', 'Pengawasan', 'Panev'])
                ->select('id_satker', 'satkernama', 'id_sakip_level', 'id_kejati')
                ->orderBy('id_sakip_level', 'asc')
                ->orderBy('satkernama', 'asc')
                ->get();
        } elseif ($evaluatorType === 'pengawasan_kejati') {
            $kejatiId = $currentUser ? $currentUser->id_kejati : null;
            $satkerList = DB::table('sinori_login')
                ->where(function ($q) use ($kejatiId, $userSatkerId) {
                    if ($kejatiId) {
                        $q->where('id_kejati', $kejatiId);
                    } else {
                        $q->where('id_satker', $userSatkerId);
                    }
                })
                ->whereIn('id_sakip_level', [3, 4])
                ->where('id_satker', 'not like', 'was%')
                ->whereNotIn('id_satker', ['admin', '999999', '888881', '888882', 'Pengawasan', 'Panev'])
                ->select('id_satker', 'satkernama', 'id_sakip_level', 'id_kejati')
                ->orderBy('id_sakip_level', 'asc')
                ->orderBy('satkernama', 'asc')
                ->get();
        } elseif ($evaluatorType === 'admin') {
            $query = DB::table('sinori_login')
                ->whereNotIn('id_satker', ['admin', '999999', '888881', '888882', 'Pengawasan', 'Panev'])
                ->where('id_satker', 'not like', 'was%');

            if ($request->filled('level') && in_array((int)$request->level, [1, 2, 3, 4])) {
                $query->where('id_sakip_level', (int)$request->level);
            }

            $satkerList = $query->select('id_satker', 'satkernama', 'id_sakip_level', 'id_kejati')
                ->orderByRaw("CASE WHEN id_sakip_level = 1 THEN 0 WHEN id_sakip_level = 2 THEN 1 ELSE 2 END")
                ->orderBy('satkernama', 'asc')
                ->get();
        } else {
            $satkerList = DB::table('sinori_login')
                ->where('id_satker', $userSatkerId)
                ->select('id_satker', 'satkernama', 'id_sakip_level', 'id_kejati')
                ->get();
        }

        $allSubkomponen = LkeSubkomponen::orderBy('id')->get();
        $allKriteria = LkeKriteria::orderBy('id')->get();
        $totalKriteriaCount = $allKriteria->count();

        $satkerIds = $satkerList->pluck('id_satker')->map(fn($id) => (string)$id)->toArray();
        $penilaianBySatker = LkePenilaianSatker::where('tahun', $tahun)
            ->whereIn('id_satker', $satkerIds)
            ->get()
            ->groupBy('id_satker');

        $koefBySatker = LkeSubkomponenKoefisien::where('tahun', $tahun)
            ->whereIn('id_satker', $satkerIds)
            ->get()
            ->groupBy('id_satker');

        $defaultBobotMap = [
            '1.A' => 6.0,  '1.B' => 9.0,  '1.C' => 15.0,
            '2.A' => 6.0,  '2.B' => 9.0,  '2.C' => 15.0,
            '3.A' => 3.0,  '3.B' => 4.5,  '3.C' => 7.5,
            '4.A' => 5.0,  '4.B' => 7.5,  '4.C' => 12.5,
        ];

        $kriteriaBySub = [];
        foreach ($allSubkomponen as $sub) {
            $kriteriaBySub[$sub->kode] = $allKriteria->filter(function ($item) use ($sub) {
                return $item->subkomponen_id == $sub->kode || $item->subkomponen_id == $sub->id;
            });
        }

        $rows = [];
        foreach ($satkerList as $stk) {
            $sid = (string)$stk->id_satker;
            $evals = $penilaianBySatker->get($sid);

            if (!$evals || $evals->isEmpty()) {
                $predD = LkeSubkomponenKoefisien::getPredikatAkip(0);
                $rows[] = [
                    'id_satker'          => $sid,
                    'satkernama'         => str_replace('_', ' ', $stk->satkernama),
                    'level'              => (int)$stk->id_sakip_level,
                    'pm_skor'            => 0.0,
                    'pm_predikat'        => $predD['kode'] ?? 'D',
                    'pm_predikat_label'  => $predD['predikat'] ?? 'Kurang',
                    'pm_evaluated_cnt'   => 0,
                    'eval_skor'          => 0.0,
                    'eval_predikat'      => $predD['kode'] ?? 'D',
                    'eval_predikat_label'=> $predD['predikat'] ?? 'Kurang',
                    'eval_evaluated_cnt' => 0,
                    'total_cnt'          => $totalKriteriaCount,
                    'selisih'            => 0.0,
                ];
                continue;
            }

            $evalMap = $evals->keyBy('kriteria_id');
            $koefMap = ($koefBySatker->get($sid) ?? collect())->keyBy('subkomponen_kode');

            $subStats = [];
            $totalPmEval = 0;
            $totalPkEval = 0;

            foreach ($allSubkomponen as $sub) {
                $subKriteria = $kriteriaBySub[$sub->kode] ?? collect();
                $tCnt = $subKriteria->count();
                $terpEval = 0;
                $terpPm = 0;

                foreach ($subKriteria as $crit) {
                    $ev = $evalMap->get($crit->kode);
                    if ($ev) {
                        if ($ev->parameter_id !== null) {
                            $totalPkEval++;
                            if ($ev->nilai === 'Terpenuhi' || (float)$ev->skor > 0) {
                                $terpEval++;
                            }
                        }
                        if ($ev->pm_parameter_id !== null) {
                            $totalPmEval++;
                            if ($ev->pm_nilai === 'Terpenuhi' || (float)$ev->pm_skor > 0) {
                                $terpPm++;
                            }
                        }
                    }
                }

                $subStats[$sub->kode] = [
                    'total_cnt'      => $tCnt,
                    'terpenuhi_eval' => $terpEval,
                    'terpenuhi_pm'   => $terpPm,
                    'is_100_eval'    => ($tCnt > 0 && $terpEval === $tCnt),
                    'is_100_pm'      => ($tCnt > 0 && $terpPm === $tCnt),
                ];
            }

            $pairGate = [];
            foreach (['1', '2', '3', '4'] as $pfx) {
                $bStat = $subStats["{$pfx}.B"] ?? ['is_100_eval' => false, 'is_100_pm' => false];
                $cStat = $subStats["{$pfx}.C"] ?? ['is_100_eval' => false, 'is_100_pm' => false];
                $pairGate[$pfx] = [
                    'eval' => ($bStat['is_100_eval'] && $cStat['is_100_eval']),
                    'pm'   => ($bStat['is_100_pm'] && $cStat['is_100_pm']),
                ];
            }

            $grandEval = 0.0;
            $grandPm = 0.0;

            foreach ($allSubkomponen as $sub) {
                $st = $subStats[$sub->kode];
                $bobot = (isset($sub->bobot) && (float)$sub->bobot > 0)
                    ? (float)$sub->bobot
                    : ($defaultBobotMap[$sub->kode] ?? 0);

                $clEval = LkeSubkomponenKoefisien::clusterPemenuhan($st['terpenuhi_eval'], $st['total_cnt']);
                $clPm   = LkeSubkomponenKoefisien::clusterPemenuhan($st['terpenuhi_pm'], $st['total_cnt']);

                $isPair = (str_ends_with($sub->kode, '.B') || str_ends_with($sub->kode, '.C'));
                $pfx = explode('.', $sub->kode)[0];
                $koefRec = $koefMap->get($sub->kode);
                $pairRec = $isPair ? ($koefMap->get("{$pfx}.C") ?: $koefMap->get("{$pfx}.B")) : $koefRec;

                if ($isPair) {
                    $kEval = $pairGate[$pfx]['eval']
                        ? (($pairRec && in_array((int)$pairRec->effective_koefisien, [80, 90, 100])) ? (int)$pairRec->effective_koefisien : 80)
                        : ($st['is_100_eval'] ? 80 : (int)$clEval['koefisien']);
                    $kPm = $pairGate[$pfx]['pm']
                        ? (($pairRec && in_array((int)$pairRec->effective_pm_koefisien, [80, 90, 100])) ? (int)$pairRec->effective_pm_koefisien : 80)
                        : ($st['is_100_pm'] ? 80 : (int)$clPm['koefisien']);
                } else {
                    $kEval = $st['is_100_eval']
                        ? (($koefRec && in_array((int)$koefRec->effective_koefisien, [80, 90, 100])) ? (int)$koefRec->effective_koefisien : 80)
                        : (int)$clEval['koefisien'];
                    $kPm = $st['is_100_pm']
                        ? (($koefRec && in_array((int)$koefRec->effective_pm_koefisien, [80, 90, 100])) ? (int)$koefRec->effective_pm_koefisien : 80)
                        : (int)$clPm['koefisien'];
                }

                $grandEval += round(($kEval / 100) * $bobot, 2);
                $grandPm   += round(($kPm / 100) * $bobot, 2);
            }

            $grandEval = round($grandEval, 2);
            $grandPm   = round($grandPm, 2);
            $predEval  = LkeSubkomponenKoefisien::getPredikatAkip($grandEval);
            $predPm    = LkeSubkomponenKoefisien::getPredikatAkip($grandPm);

            $rows[] = [
                'id_satker'          => $sid,
                'satkernama'         => str_replace('_', ' ', $stk->satkernama),
                'level'              => (int)$stk->id_sakip_level,
                'pm_skor'            => $grandPm,
                'pm_predikat'        => $predPm['kode'] ?? 'D',
                'pm_predikat_label'  => $predPm['predikat'] ?? '-',
                'pm_evaluated_cnt'   => $totalPmEval,
                'eval_skor'          => $grandEval,
                'eval_predikat'      => $predEval['kode'] ?? 'D',
                'eval_predikat_label'=> $predEval['predikat'] ?? '-',
                'eval_evaluated_cnt' => $totalPkEval,
                'total_cnt'          => $totalKriteriaCount,
                'selisih'            => round($grandEval - $grandPm, 2),
            ];
        }

        usort($rows, function ($a, $b) {
            if ($b['eval_skor'] != $a['eval_skor']) return $b['eval_skor'] <=> $a['eval_skor'];
            if ($b['pm_skor'] != $a['pm_skor']) return $b['pm_skor'] <=> $a['pm_skor'];
            return strcmp($a['satkernama'], $b['satkernama']);
        });

        return response()->json([
            'success' => true,
            'tahun'   => $tahun,
            'total'   => count($rows),
            'data'    => $rows,
        ]);
    }
```

### 2.2. Tombol & Modal Rekapitulasi Peringkat + Export Kertas Kerja LKE ke Excel
- **File Target**: [`resources/views/lke/evaluasi/index.blade.php`](file:///c:/Users/Sicana%20350/Documents/Project/laravel11/resources/views/lke/evaluasi/index.blade.php)
- Tambahkan tombol `#btnOpenRekapRanking` dan `#btnExportLkeExcel` pada header halaman (baris 59–65), serta modal `#modalRekapRankingLke` dan script SheetJS di akhir file.

---

## Tahap 3 — Kelengkapan Seeder Cascading 2026 Seluruh 9 Unit Eselon I termasuk JAMBIN (Kategori B2)

- **File Target**:
  1. [`database/seeders/SasaranProgramSeeder.php`](file:///c:/Users/Sicana%20350/Documents/Project/laravel11/database/seeders/SasaranProgramSeeder.php)
  2. [`database/seeders/IndikatorKinerjaProgramSeeder.php`](file:///c:/Users/Sicana%20350/Documents/Project/laravel11/database/seeders/IndikatorKinerjaProgramSeeder.php)
  3. [`database/seeders/TargetIkpSeeder.php`](file:///c:/Users/Sicana%20350/Documents/Project/laravel11/database/seeders/TargetIkpSeeder.php)
  4. [`database/seeders/DatabaseSeeder.php`](file:///c:/Users/Sicana%20350/Documents/Project/laravel11/database/seeders/DatabaseSeeder.php)

**Data Sasaran Program & IKP JAMBIN (`005016`) yang ditambahkan ke Seeder:**
```php
// Pada SasaranProgramSeeder.php:
[
    'kode_sp' => 'SP_BIN_SDM',
    'nama_sp' => 'Terwujudnya tata kelola sumber daya manusia aparatur Kejaksaan yang profesional dan berintegritas',
    'id_satker' => '005016',
    'is_crosscutting' => 0,
],
[
    'kode_sp' => 'SP_BIN_KEU',
    'nama_sp' => 'Meningkatnya kualitas pengelolaan keuangan negara, BMN, dan penerimaan negara bukan pajak (PNBP)',
    'id_satker' => '005016',
    'is_crosscutting' => 0,
],
[
    'kode_sp' => 'SP_BIN_RB',
    'nama_sp' => 'Meningkatnya kualitas tata kelola organisasi, perencanaan strategis, dan akuntabilitas kinerja (SAKIP & RB)',
    'id_satker' => '005016',
    'is_crosscutting' => 0,
],
```

---

## Tahap 4 — Optimasi Performa Query, Keamanan & Pembersihan File Duplikat (Kategori C)

### 4.1. Optimasi Query & Caching pada `SakipwilController`
- **File Target**: [`app/Http/Controllers/SakipwilController.php`](file:///c:/Users/Sicana%20350/Documents/Project/laravel11/app/Http/Controllers/SakipwilController.php)
- Bungkus hasil pengambilan matriks kepatuhan dokumen wilayah dengan `Cache::remember("sakipwil_{$id_satker}_{$tahun}", 180, function () { ... })` dan hapus method `search()` yang memanggil tabel tidak valid `DB::table('id_satker')`.

### 4.2. Perbaikan Query `exportExcel` pada `MonitoringController`
- **File Target**: [`app/Http/Controllers/MonitoringController.php`](file:///c:/Users/Sicana%20350/Documents/Project/laravel11/app/Http/Controllers/MonitoringController.php#L326-L399)
- Ganti `$tahun = 2026;` dengan `$tahun = session('tahun_terpilih', date('Y'));` dan hapus seleksi kolom `t.target_triwulan_1..4` yang tidak ada pada tabel `target`.

### 4.3. Pembersihan File Salinan / Backup Tidak Terpakai
Perintah PowerShell untuk menghapus 7 file duplikat yang mengotori workspace:
```powershell
Remove-Item -LiteralPath `
  "routes/web copy.php", `
  "app/Http/Controllers/EvaluasiController - Copy.php", `
  "app/Http/Controllers/PelaporanController copy.php", `
  "app/Http/Controllers/PelaporanController copy 2.php", `
  "resources/views/kelola/lke.blade - Copy.php", `
  "resources/views/kelola/x pelaporan.blade copy.php", `
  "app/Models/Renaksi_old.php" -ErrorAction SilentlyContinue
```
