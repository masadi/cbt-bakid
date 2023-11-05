<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mata_pelajaran extends Model
{
    use HasFactory;
    protected $table = 'mata_pelajaran';
	protected $primaryKey = 'mata_pelajaran_id';
	protected $guarded = [];
    public function pembelajaran()
    {
        return $this->belongsTo(Pembelajaran::class, 'mata_pelajaran_id', 'mata_pelajaran_id');
    }
}
