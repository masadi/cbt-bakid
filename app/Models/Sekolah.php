<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Support\Database\CacheQueryBuilder;
use Illuminate\Database\Eloquent\SoftDeletes;
class Sekolah extends Model
{
	use SoftDeletes;
    public $incrementing = false;
	protected $table = 'sekolah';
	public $keyType = 'string';
	protected $primaryKey = 'sekolah_id';
	protected $guarded = [];
	public function guru(){
		return $this->hasOne('App\Guru', 'guru_id', 'guru_id');
	}
	public function ptk(){
		return $this->hasOne(Guru::class, 'sekolah_id', 'sekolah_id');
	}
	public function pd(){
		return $this->hasOne(Peserta_didik::class, 'sekolah_id', 'sekolah_id');
	}
	public function provinsi()
	{
		return $this->hasOne(Mst_wilayah::class, 'kode_wilayah', 'provinsi_id');
	}
	public function kabupaten()
	{
		return $this->hasOne(Mst_wilayah::class, 'kode_wilayah', 'kabupaten_id');
	}
	public function kecamatan()
	{
		return $this->hasOne(Mst_wilayah::class, 'kode_wilayah', 'kecamatan_id');
	}
	public function desa()
	{
		return $this->hasOne(Mst_wilayah::class, 'kode_wilayah', 'desa_id');
	}
	public function user()
	{
		return $this->hasOne(User::class, 'sekolah_id', 'sekolah_id');
	}
	public function blangko()
	{
		return $this->hasMany(Blangko::class, 'sekolah_id', 'sekolah_id');
	}
	public function blangko_3()
	{
		return $this->hasMany(Blangko::class, 'sekolah_id', 'sekolah_id')->where('tahun', 3)->whereNull('jumlah');
	}
    public function blangko_4()
	{
		return $this->hasMany(Blangko::class, 'sekolah_id', 'sekolah_id')->where('tahun', 4)->whereNull('jumlah');
	}
	public function temp(){
		return $this->hasOne(Sekolah_temp::class, 'sekolah_id', 'sekolah_id');
	}
	public function pusdatin(){
		return $this->hasOne(Pusdatin::class, 'npsn', 'npsn');
	}
}
