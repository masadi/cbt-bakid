<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Ujian extends Model
{
    use HasFactory, Uuid;
    public $incrementing = false;
	protected $table = 'ujian';
	protected $primaryKey = 'ujian_id';
	protected $guarded = [];
	public function soal()
	{
		return $this->hasMany(Soal::class, 'ujian_id', 'ujian_id');
	}
	public function pembelajaran()
	{
		return $this->hasOne(Pembelajaran::class, 'pembelajaran_id', 'pembelajaran_id');
	}
	public function jawaban_siswa()
    {
        return $this->hasManyThrough(
            Jawaban_siswa::class,
            Soal::class,
            'ujian_id', // Foreign key on the environments table...
            'soal_id', // Foreign key on the deployments table...
            'ujian_id', // Local key on the projects table...
            'soal_id' // Local key on the environments table...
        );
    }
	public function ujian_siswa()
	{
		return $this->hasOne(Ujian_siswa::class, 'ujian_id', 'ujian_id');
	}
	public function all_ujian_siswa()
	{
		return $this->hasMany(Ujian_siswa::class, 'ujian_id', 'ujian_id');
	}
}
