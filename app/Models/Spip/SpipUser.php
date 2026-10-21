<?php
namespace App\Models\Spip;

use Illuminate\Foundation\Auth\User as Authenticatable;

class SpipUser extends Authenticatable
{
    protected $table = 'spip_users';

    protected $fillable = [
        'satker',
        'username',
        'password_pm',
        'password_pk'
    ];
}
