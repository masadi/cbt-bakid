<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Soal extends Model
{
    use HasFactory, Uuid;
    public $incrementing = false;
	protected $table = 'soal';
	protected $primaryKey = 'soal_id';
	protected $guarded = [];
	public function jawaban()
	{
		return $this->hasMany(Jawaban::class, 'soal_id', 'soal_id');
	}
	public function jawaban_siswa()
	{
		return $this->hasOne(Jawaban_siswa::class, 'soal_id', 'soal_id');
	}
	public function ujian()
	{
		return $this->hasOne(Ujian::class, 'ujian_id', 'ujian_id');
	}
}
