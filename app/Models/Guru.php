<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Guru extends Model
{
    use HasFactory, Uuid;
    public $incrementing = false;
	protected $table = 'guru';
	protected $primaryKey = 'guru_id';
	protected $guarded = [];
	public function user()
	{
		return $this->hasOne(User::class, 'guru_id', 'guru_id');
	}
	public function pembelajaran(){
		return $this->hasMany(Pembelajaran::class, 'guru_id', 'guru_id');
    }
}
