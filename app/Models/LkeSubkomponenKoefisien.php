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

    const GRADASI_E  = 0;
    const GRADASI_D  = 30;
    const GRADASI_C  = 50;
    const GRADASI_CC = 60;
    const GRADASI_B  = 70;
    const GRADASI_BB = 80;
    const GRADASI_A  = 90;
    const GRADASI_AA = 100;

    /**
     * Hitung kluster gradasi dan koefisien berdasarkan persentase kriteria terpenuhi:
     * - E  : 0%               => Koefisien 0%
     * - D  : > 0% - 25%       => Koefisien 30%
     * - C  : > 25% - 50%      => Koefisien 50%
     * - CC : > 50% - 75%      => Koefisien 60%
     * - B  : > 75% - < 100%   => Koefisien 70%
     * - BB : 100%             => Koefisien 80% (Standar maksimal sebelum opsi A/AA)
     */
    public static function clusterPemenuhan($terpenuhiCount, $totalCount): array
    {
        $terpenuhi = (int)$terpenuhiCount;
        $total = (int)$totalCount;

        if ($total <= 0 || $terpenuhi <= 0) {
            return [
                'gradasi'    => 'E',
                'koefisien'  => self::GRADASI_E,
                'persen'     => 0.0,
            ];
        }

        $persen = round(($terpenuhi / $total) * 100, 2);

        if ($terpenuhi >= $total || $persen >= 100.0) {
            return [
                'gradasi'    => 'BB',
                'koefisien'  => self::GRADASI_BB,
                'persen'     => 100.0,
            ];
        }

        if ($persen > 75.0) {
            return [
                'gradasi'    => 'B',
                'koefisien'  => self::GRADASI_B,
                'persen'     => $persen,
            ];
        }

        if ($persen > 50.0) {
            return [
                'gradasi'    => 'CC',
                'koefisien'  => self::GRADASI_CC,
                'persen'     => $persen,
            ];
        }

        if ($persen > 25.0) {
            return [
                'gradasi'    => 'C',
                'koefisien'  => self::GRADASI_C,
                'persen'     => $persen,
            ];
        }

        return [
            'gradasi'    => 'D',
            'koefisien'  => self::GRADASI_D,
            'persen'     => $persen,
        ];
    }

    /**
     * Helper kelas badge Bootstrap berdasarkan gradasi nilai.
     */
    public static function getGradasiBadgeClass(?string $gradasi): string
    {
        return match ($gradasi) {
            'AA'    => 'bg-success text-white',
            'A'     => 'bg-info text-dark',
            'BB'    => 'bg-primary text-white',
            'B'     => 'bg-primary-subtle text-primary border border-primary-subtle',
            'CC'    => 'bg-warning-subtle text-dark border border-warning-subtle',
            'C'     => 'bg-warning text-dark',
            'D'     => 'bg-danger text-white',
            'E'     => 'bg-secondary text-white',
            default => 'bg-secondary text-white',
        };
    }

    /**
     * Hitung Predikat Akuntabilitas Kinerja Instansi Pemerintah (AKIP) berdasarkan Grand Total Skor:
     * - > 90 - 100 : AA (Sangat Memuaskan)
     * - > 80 - 90  : A  (Memuaskan)
     * - > 70 - 80  : BB (Sangat Baik)
     * - > 60 - 70  : B  (Baik)
     * - > 50 - 60  : CC (Cukup)
     * - > 30 - 50  : C  (Kurang)
     * - 0 - 30     : D  (Sangat Kurang)
     */
    public static function getPredikatAkip($score): array
    {
        $val = (float)$score;
        if ($val > 90.0) {
            return ['predikat' => 'AA', 'keterangan' => 'Sangat Memuaskan', 'badge' => 'bg-success text-white'];
        }
        if ($val > 80.0) {
            return ['predikat' => 'A', 'keterangan' => 'Memuaskan', 'badge' => 'bg-info text-dark'];
        }
        if ($val > 70.0) {
            return ['predikat' => 'BB', 'keterangan' => 'Sangat Baik', 'badge' => 'bg-primary text-white'];
        }
        if ($val > 60.0) {
            return ['predikat' => 'B', 'keterangan' => 'Baik', 'badge' => 'bg-primary-subtle text-primary border border-primary-subtle'];
        }
        if ($val > 50.0) {
            return ['predikat' => 'CC', 'keterangan' => 'Cukup', 'badge' => 'bg-warning-subtle text-dark border border-warning-subtle'];
        }
        if ($val > 30.0) {
            return ['predikat' => 'C', 'keterangan' => 'Kurang', 'badge' => 'bg-warning text-dark'];
        }
        return ['predikat' => 'D', 'keterangan' => 'Sangat Kurang', 'badge' => 'bg-danger text-white'];
    }

    /**
     * Get effective coefficient for Evaluasi (JAMWAS / Kejati).
     * Returns 100 for AA, 90 for A, 80 for BB / default.
     */
    public function getEffectiveKoefisienAttribute()
    {
        if (in_array($this->nilai, ['AA', '100', 100])) return self::GRADASI_AA;
        if (in_array($this->nilai, ['A', '90', 90])) return self::GRADASI_A;
        if (in_array($this->nilai, ['BB', '80', 80])) return self::GRADASI_BB;
        if (in_array($this->nilai, ['B', '70', 70])) return self::GRADASI_B;
        if (in_array($this->nilai, ['CC', '60', 60])) return self::GRADASI_CC;
        if (in_array($this->nilai, ['C', '50', 50])) return self::GRADASI_C;
        if (in_array($this->nilai, ['D', '30', 30])) return self::GRADASI_D;
        if (in_array($this->nilai, ['E', '0', 0])) return self::GRADASI_E;
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
        if (in_array($this->pm_nilai, ['B', '70', 70])) return self::GRADASI_B;
        if (in_array($this->pm_nilai, ['CC', '60', 60])) return self::GRADASI_CC;
        if (in_array($this->pm_nilai, ['C', '50', 50])) return self::GRADASI_C;
        if (in_array($this->pm_nilai, ['D', '30', 30])) return self::GRADASI_D;
        if (in_array($this->pm_nilai, ['E', '0', 0])) return self::GRADASI_E;
        if ($this->pm_nilai === 'Terpenuhi') return (int)($this->pm_target_koefisien ?: self::GRADASI_BB);
        return (int)($this->pm_koefisien ?: self::GRADASI_BB);
    }

    /**
     * Get normalized gradasi label ('E', 'D', 'C', 'CC', 'B', 'BB', 'A', 'AA').
     */
    public function getGradasiLabelAttribute()
    {
        $eff = $this->effective_koefisien;
        return match ($eff) {
            100 => 'AA',
            90  => 'A',
            80  => 'BB',
            70  => 'B',
            60  => 'CC',
            50  => 'C',
            30  => 'D',
            0   => 'E',
            default => 'BB',
        };
    }

    /**
     * Get normalized PM gradasi label ('E', 'D', 'C', 'CC', 'B', 'BB', 'A', 'AA').
     */
    public function getPmGradasiLabelAttribute()
    {
        $eff = $this->effective_pm_koefisien;
        return match ($eff) {
            100 => 'AA',
            90  => 'A',
            80  => 'BB',
            70  => 'B',
            60  => 'CC',
            50  => 'C',
            30  => 'D',
            0   => 'E',
            default => 'BB',
        };
    }
}
