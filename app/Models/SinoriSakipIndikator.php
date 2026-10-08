<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SinoriSakipIndikator extends Model
{
    use HasFactory;

    protected $table = 'sinori_sakip_indikator'; // Nama tabel

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tipe',
        'link',
        'lingkup',
        'indikator_nama',
        'indikator_pembilang',
        'indikator_penyebut',
        'target_indikator',
        'indikator_new',
    ];
    const INDICATOR_IDS = [22, 23, 24, 25];

    public static function getData()
    {
        return self::whereIn('id', self::INDICATOR_IDS)
            ->get();
    }
}
