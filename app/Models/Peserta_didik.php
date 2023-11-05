<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Peserta_didik extends Model
{
    use HasFactory, Uuid;
	public $incrementing = false;
	protected $table = 'peserta_didik';
	protected $primaryKey = 'peserta_didik_id';
	protected $guarded = [];
	
	public function user()
	{
		return $this->hasOne(User::class, 'peserta_didik_id', 'peserta_didik_id');
	}
	public function jawaban_siswa()
    {
        //return $this->hasMany(Jawaban_siswa::class, 'user_id', 'user_id');
        return $this->hasManyThrough(
            Jawaban_siswa::class,
            User::class,
            'peserta_didik_id', // Foreign key on the environments table...
            'user_id', // Foreign key on the deployments table...
            'peserta_didik_id', // Local key on the projects table...
            'user_id' // Local key on the environments table...
        );
    }
    public function kelas(){
		return $this->hasOneThrough(
            Rombongan_belajar::class,
            Anggota_rombel::class,
            'peserta_didik_id', // Foreign key on the cars table...
            'rombongan_belajar_id', // Foreign key on the owners table...
            'peserta_didik_id', // Local key on the mechanics table...
            'rombongan_belajar_id' // Local key on the cars table...
        );
	}
}
