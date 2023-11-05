<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Pembelajaran extends Model
{
    use HasFactory, Uuid;
    public $incrementing = false;
	protected $table = 'pembelajaran';
	protected $primaryKey = 'pembelajaran_id';
	protected $guarded = [];
	public function ujian()
	{
		return $this->hasMany(Ujian::class, 'pembelajaran_id', 'pembelajaran_id');
	}
	public function rombongan_belajar()
	{
		return $this->hasOne(Rombongan_belajar::class, 'rombongan_belajar_id', 'rombongan_belajar_id');
	}
	public function guru()
	{
		return $this->hasOne(Guru::class, 'guru_id', 'guru_id');
	}
}
