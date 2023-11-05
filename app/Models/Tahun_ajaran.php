<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tahun_ajaran extends Model
{
    public $incrementing = false;
	protected $table = 'tahun_ajaran';
	protected $primaryKey = 'tahun_ajaran_id';
	protected $guarded = [];
	public function semester(){
		return $this->hasMany(Semester::class, 'tahun_ajaran_id', 'tahun_ajaran_id')->orderBy('semester_id', 'asc');
    }
}
