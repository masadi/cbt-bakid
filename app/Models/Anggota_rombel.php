<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;

class Anggota_rombel extends Model
{
	use HasFactory, Uuid;
	public $incrementing = false;
	protected $table = 'anggota_rombel';
	protected $primaryKey = 'anggota_rombel_id';
	protected $guarded = [];
}
