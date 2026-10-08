<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LkeKomponen extends Model
{
    protected $table = 'lke_komponen';

    protected $fillable = [
        'no',
        'nama',
        'bobot',
        'tahun',
    ];

    public function subkomponen()
    {
        return $this->hasMany(LkeSubkomponen::class, 'komponen_id', 'id');
    }
}
