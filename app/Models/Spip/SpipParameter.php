<?php
// app/Models/Spip/SpipParameter.php
namespace App\Models\Spip;

use Illuminate\Database\Eloquent\Model;

class SpipParameter extends Model {
    protected $table = 'spip_parameters';

    // Pastikan kolom baru bisa diisi melalui mass assignment
    protected $fillable = [
        'kode', 'sub_unsur', 'kode_sub_unsur', 'uraian_parameter',
        'spip', 'mri', 'iepk'
    ];

    public function criteria() {
        return $this->hasMany(SpipCriteria::class, 'parameter_id');
    }
}
