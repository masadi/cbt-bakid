<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CetakController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\AuthController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['prefix' => 'cetak'], function () {
    Route::get('/', [CetakController::class, 'viewPdf']);
    Route::group(['prefix' => 'berita-acara'], function () {
        Route::get('/{jenis}/{user_id}', [CetakController::class, 'berita_acara']);
    });
});
Route::group(['prefix' => 'downloads'], function () {
    Route::get('/template-excel/{sekolah_id}', [DownloadController::class, 'template_excel']);
});
//Route::get('/auth/sso', [AuthController::class, 'sso']);
Route::get('/{any}', [ApplicationController::class, 'index'])->where('any', '.*');