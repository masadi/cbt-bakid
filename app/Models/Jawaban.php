<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Jawaban extends Model
{
    use HasFactory, Uuid;
    public $incrementing = false;
	protected $table = 'jawaban';
	protected $primaryKey = 'jawaban_id';
	protected $guarded = [];
	public function soal()
	{
		return $this->hasOne(Soal::class, 'soal_id', 'soal_id');
	}
}
