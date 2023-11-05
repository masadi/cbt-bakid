<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UjianController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\PdController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\MapelController;
use App\Http\Controllers\RombelController;
use App\Http\Controllers\SoalController;
use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('no-access', [DashboardController::class, 'no_access'])->name('login');
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    
    Route::group(['middleware' => 'auth:sanctum'], function() {
      Route::get('logout', [AuthController::class, 'logout']);
      Route::get('user', [UserController::class, 'profile']);
      Route::post('update', [UserController::class, 'update_profile']);
      Route::post('hapus', [UserController::class, 'hapus']);
    });
});
Route::group(['middleware' => 'auth:sanctum'], function () {
  Route::group(['prefix' => 'general'], function () {
    Route::get('/index', [ApiController::class, 'index']);
    Route::post('upload', [ApiController::class, 'upload']);
  });
  Route::group(['prefix' => 'ujian'], function () {
    Route::post('index', [UjianController::class, 'index']);
    Route::post('simpan', [UjianController::class, 'simpan']);
    Route::post('waktu', [UjianController::class, 'waktu']);
    Route::post('selesai', [UjianController::class, 'selesai']);
    Route::post('hasil', [UjianController::class, 'hasil']);
  });
  Route::group(['prefix' => 'dashboard'], function () {
    Route::get('/', [DashboardController::class, 'index']);
  });
  Route::group(['prefix' => 'guru'], function () {
    Route::get('/', [GuruController::class, 'index']);
    Route::post('/hapus', [GuruController::class, 'hapus']);
    Route::post('/update', [GuruController::class, 'update']);
  });
  Route::group(['prefix' => 'peserta-didik'], function () {
    Route::get('/', [PdController::class, 'index']);
    Route::post('/hapus', [PdController::class, 'hapus']);
    Route::post('/update', [PdController::class, 'update']);
  });
  Route::group(['prefix' => 'mata-pelajaran'], function () {
    Route::get('/', [MapelController::class, 'index']);
    Route::post('/hapus', [MapelController::class, 'hapus']);
    Route::post('/update', [MapelController::class, 'update']);
  });
  Route::group(['prefix' => 'rombongan-belajar'], function () {
    Route::get('/', [RombelController::class, 'index']);
    Route::post('/pembelajaran', [RombelController::class, 'pembelajaran']);
    Route::post('/wali-kelas', [RombelController::class, 'wali_kelas']);
    Route::post('/simpan-wali-kelas', [RombelController::class, 'simpan_wali_kelas']);
    Route::post('/simpan-pembelajaran', [RombelController::class, 'simpan_pembelajaran']);
    Route::post('/hapus-pembelajaran', [RombelController::class, 'hapus_pembelajaran']);
  });
  Route::group(['prefix' => 'bank-soal'], function () {
    Route::get('/', [SoalController::class, 'index']);
    Route::post('/soal', [SoalController::class, 'soal']);
    Route::post('/simpan', [SoalController::class, 'simpan']);
    Route::post('/upload', [SoalController::class, 'upload']);
  });
});
