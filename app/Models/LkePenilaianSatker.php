<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LkePenilaianSatker extends Model
{
    use HasFactory;

    protected $table = 'lke_penilaian_satker';

    protected $fillable = [
        'id_satker',
        'tahun',
        'komponen_id',
        'subkomponen_id',
        'kriteria_id',
        'pm_parameter_id',
        'pm_nilai',
        'pm_skor',
        'pm_catatan',
        'pm_evaluator_id',
        'parameter_id',
        'nilai',
        'skor',
        'catatan',
        'evaluator_id',
    ];

    public function kriteria()
    {
        return $this->belongsTo(LkeKriteria::class, 'kriteria_id', 'kode');
    }

    public function parameter()
    {
        return $this->belongsTo(LkeParameter::class, 'parameter_id', 'id');
    }

    public function pmParameter()
    {
        return $this->belongsTo(LkeParameter::class, 'pm_parameter_id', 'id');
    }
}
