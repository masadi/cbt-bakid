<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Rombongan_belajar extends Model
{
	use HasFactory, Uuid;
	public $incrementing = false;
	protected $table = 'rombongan_belajar';
	protected $primaryKey = 'rombongan_belajar_id';
	protected $guarded = [];
	
	public function anggota_rombel()
	{
		return $this->hasMany(Anggota_rombel::class, 'rombongan_belajar_id', 'rombongan_belajar_id');
	}
	public function pembelajaran()
	{
		return $this->hasMany(Pembelajaran::class, 'rombongan_belajar_id', 'rombongan_belajar_id');
	}
	public function walas()
	{
		return $this->belongsTo(Guru::class, 'guru_id', 'guru_id');
	}
}
