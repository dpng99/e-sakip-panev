<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LkeSubkomponen extends Model
{
    protected $table = 'lke_subkomponen';

    protected $fillable = [
        'komponen_id',
        'kode',
        'nama',
        'bobot',
        'tahun',
    ];

    public function komponen()
    {
        return $this->belongsTo(LkeKomponen::class, 'komponen_id', 'id');
    }

    public function kriteria()
    {
        return $this->hasMany(LkeKriteria::class, 'subkomponen_id', 'kode');
    }
}
