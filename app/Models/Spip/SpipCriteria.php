<?php
namespace App\Models\Spip;
use Illuminate\Database\Eloquent\Model;

class SpipCriteria extends Model {
    protected $table = 'spip_criteria';

    public function parameter() {
    return $this->belongsTo(SpipParameter::class, 'parameter_id');
}
}
