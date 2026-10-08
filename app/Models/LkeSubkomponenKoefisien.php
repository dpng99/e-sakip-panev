<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LkeSubkomponenKoefisien extends Model
{
    protected $table = 'lke_subkomponen_koefisien';

    protected $fillable = [
        'id_satker',
        'tahun',
        'subkomponen_kode',
        'target_koefisien',
        'uraian',
        'bukti_dukung',
        'link_bukti_dukung',
        'nilai',
        'koefisien',
        'evaluator_id',
        'pm_target_koefisien',
        'pm_uraian',
        'pm_bukti_dukung',
        'pm_link_bukti_dukung',
        'pm_nilai',
        'pm_koefisien',
        'pm_evaluator_id',
    ];

    const GRADASI_BB = 80;
    const GRADASI_A  = 90;
    const GRADASI_AA = 100;

    /**
     * Get effective coefficient for Evaluasi (JAMWAS / Kejati).
     * Returns 100 for AA, 90 for A, 80 for BB / default.
     */
    public function getEffectiveKoefisienAttribute()
    {
        if (in_array($this->nilai, ['AA', '100', 100])) return self::GRADASI_AA;
        if (in_array($this->nilai, ['A', '90', 90])) return self::GRADASI_A;
        if (in_array($this->nilai, ['BB', '80', 80])) return self::GRADASI_BB;
        if ($this->nilai === 'Terpenuhi') return (int)($this->target_koefisien ?: self::GRADASI_BB);
        return (int)($this->koefisien ?: self::GRADASI_BB);
    }

    /**
     * Get effective coefficient for Penilaian Mandiri (PM Satker).
     * Returns 100 for AA, 90 for A, 80 for BB / default.
     */
    public function getEffectivePmKoefisienAttribute()
    {
        if (in_array($this->pm_nilai, ['AA', '100', 100])) return self::GRADASI_AA;
        if (in_array($this->pm_nilai, ['A', '90', 90])) return self::GRADASI_A;
        if (in_array($this->pm_nilai, ['BB', '80', 80])) return self::GRADASI_BB;
        if ($this->pm_nilai === 'Terpenuhi') return (int)($this->pm_target_koefisien ?: self::GRADASI_BB);
        return (int)($this->pm_koefisien ?: self::GRADASI_BB);
    }

    /**
     * Get normalized gradasi label ('BB', 'A', 'AA', or null).
     */
    public function getGradasiLabelAttribute()
    {
        $eff = $this->effective_koefisien;
        return match ($eff) {
            100 => 'AA',
            90  => 'A',
            default => 'BB',
        };
    }

    /**
     * Get normalized PM gradasi label ('BB', 'A', 'AA', or null).
     */
    public function getPmGradasiLabelAttribute()
    {
        $eff = $this->effective_pm_koefisien;
        return match ($eff) {
            100 => 'AA',
            90  => 'A',
            default => 'BB',
        };
    }
}
