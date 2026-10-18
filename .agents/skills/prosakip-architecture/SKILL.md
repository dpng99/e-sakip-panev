---
name: prosakip-architecture
description: >-
  Comprehensive architectural reference and development runbook for the PROSAKIP Kejaksaan RI
  (Laravel 11) application. Use this skill whenever planning features, debugging routes,
  working with satker hierarchy/RBAC, modifying LKE evaluation, or extending Cascading SP/IKP.
---

# PROSAKIP Kejaksaan RI — Architecture & Development Runbook

## 1. Organizational Hierarchy & RBAC Invariants (`sinori_login`)

| Role / Tier | `id_sakip_level` | `id_satker` Pattern | Jurisdiction & Access Rights |
| :--- | :--- | :--- | :--- |
| **Administrator** | `99` | `admin`, `999999`, `888881` | Full CRUD, master data, satker impersonation (`pilih_satker`), global LKE & LHE access. |
| **JAMWAS Evaluator Pusat** | `0` | `888882`, `Panev` | Evaluates **Level 1 (9 Eselon I)** and **Level 2 (Kejati)** in LKE & LHE AKIP. Blocked from operational satker input (`RestrictPengawasanAccess`). Cannot perform PM (`419346` is the PM account for JAMWAS). |
| **Pengawasan Kejati** | `0` / `2` | `wasxxxxxx` (e.g., `was005083`) | Evaluates **Level 3 (Kejari)** and **Level 4 (Cabjari)** within matching `id_kejati`. Blocked from operational satker input (`RestrictPengawasanAccess`). |
| **Level 1 (Eselon I Pusat)** | `1` | `005016` (JAMBIN), `419345` (JAMINTEL), `418326` (JAMPIDUM), `419344` (JAMPIDSUS), `417023` (JAMDATUN), `677111` (JAMPIDMIL), `691270` (BPA), `666405` (BADIKLAT), `419346` (JAMWAS PM) | Uses Cascading 2026 (`sasaran_program`, `indikator_kinerja_program`, `target_ikp`, `pengukuran` with `khusus = 2`, `pelaporan_ikp`). |
| **Level 2 (Kejati)** | `2` | 2-digit / 6-digit Kejati code | Uses standard PK (`target`), monthly `pengukuran`, `sinori_sakip_*` uploads, and LKE PM. |
| **Level 3 (Kejari)** | `3` | Kejari satker code | Same as Level 2; evaluated by `wasxxxxxx` of its parent `id_kejati`. |
| **Level 4 (Cabjari)** | `4` | Cabjari satker code | Same as Level 3; evaluated by `wasxxxxxx` of its parent `id_kejati`. |

## 2. Database Schema Map (Legacy `sinori_*` vs. Modern Tables)

1. **Document Repository Tables (Versioned via `id_perubahan`, stored in `public/uploads/repository/{id_satker}/`)**:
   - `sinori_sakip_renstra` (`id_periode` = `'P1'` for 2024, `'P2'` for 2025–2029)
   - `sinori_sakip_iku`, `sinori_sakip_renja`, `sinori_sakip_rkakl`, `sinori_sakip_dipa`, `sinori_sakip_renaksi`, `pk`
   - `sinori_sakip_lakip` (LKJIP), `sinori_sakip_rastaff` (Rapat Staff EKA), `sinori_sakip_renaksieval` (Monev Renaksi)
   - `lhe` (LHE AKIP), `tl_lhe_akip` (TL LHE AKIP), `sinori_sakip_keputusan` (KEP Tim SAKIP)
2. **Performance Cascading & Measurement Tables**:
   - Levels 2–4: `sinori_sakip_bidang`, `sinori_sakip_saspro`, `sinori_sakip_indikator` (`VARCHAR(50)` PK), `target` (`TargetPK`), `pengukuran` (`khusus = 0/1`).
   - Level 1 (Eselon I): `sasaran_program`, `indikator_kinerja_program` (`sifat_node`: `AVERAGE`, `RATIO_PERCENTAGE`, `INDEX_SCORE`, `DIRECT_INPUT`, `UNFAVORABLE`), `target_ikp`, `pengukuran` (`khusus = 2`), `pelaporan_ikp`.
3. **LKE Evaluation Engine Tables**:
   - Master: `lke_komponen`, `lke_subkomponen`, `lke_kriteria`, `lke_parameter`, `lke_buktidukung`.
   - Transactional: `lke_penilaian_satker` (stores both `pm_*` and evaluator scores), `lke_subkomponen_koefisien` (stores `80`/`90`/`100` gradation with 100% completion gate), `lke_satker_bukti` (synced with legacy `bukti_dukung` via `App\Helpers\LkeEvidenceHelper`).

## 3. Core Invariants & Checklist Before Modifying Code
- **Subfolder URL Safety**: Always use `{{ url(...) }}` or `{{ route(...) }}` in Blade/JS; never hardcode `/path`.
- **Upload Path Consistency**: Always store satker repository files in `public_path('uploads/repository/' . $idSatker)` and link via `asset('uploads/repository/' . $idSatker . '/' . $filename)`.
- **Dual-Sync SQL Dumps**: When adding migrations/seeders for 2026, keep `database/sql_2026/` idempotent (`CREATE TABLE IF NOT EXISTS`, `REPLACE INTO`) for cPanel phpMyAdmin deployment.
