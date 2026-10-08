<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LkeKriteria extends Model
{
    protected $table = 'lke_kriteria';

    protected $fillable = [
        'subkomponen_id',
        'kode',
        'nama',
        'dokumen_bukti',
        'kode_bukti',
        'eselon_i',
        'kejati',
        'kejari',
        'cabjari',
        'tahun',
    ];

    public function subkomponen()
    {
        return $this->belongsTo(LkeSubkomponen::class, 'subkomponen_id', 'kode');
    }

    public function parameters()
    {
        $relation = $this->hasMany(LkeParameter::class, 'kriteria_id', 'kode');
        if (!empty($this->tahun)) {
            $relation->where('tahun', $this->tahun);
        }
        return $relation;
    }

    public function gabungan()
    {
        return $this->hasMany(LkeGabungan::class, 'kriteria_id', 'kode');
    }

    public function buktidukung()
    {
        return $this->belongsToMany(
            LkeBuktidukung::class,
            'lke_gabungan',
            'kriteria_id',
            'buktidukung_id',
            'kode',
            'id'
        );
    }

    public function satkerBukti()
    {
        return $this->hasMany(LkeSatkerBukti::class, 'kriteria_id', 'kode');
    }

    public function penilaianSatker()
    {
        return $this->hasMany(LkePenilaianSatker::class, 'kriteria_id', 'kode');
    }

    /**
     * Get array of buktidukung IDs from comma-separated kode_bukti.
     */
    public function getBuktiDukungIdsAttribute()
    {
        if (empty($this->kode_bukti)) {
            return [];
        }
        return array_map('intval', array_map('trim', explode(',', $this->kode_bukti)));
    }
}
