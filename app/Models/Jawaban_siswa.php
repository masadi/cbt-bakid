<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Jawaban_siswa extends Model
{
    use HasFactory, Uuid;
    public $incrementing = false;
	protected $table = 'jawaban_siswa';
	protected $primaryKey = 'jawaban_siswa_id';
	protected $guarded = [];
	public function user()
	{
		return $this->hasOne(User::class, 'user_id', 'user_id');
	}
	public function jawaban()
	{
		return $this->hasOne(Jawaban::class, 'jawaban_id', 'jawaban_id');
	}
	public function soal()
	{
		return $this->hasOne(Soal::class, 'soal_id', 'soal_id');
	}
}
