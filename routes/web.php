<?php

use Illuminate\Support\Facades\Route;

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
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\MahasiswaController;
Route::get('articles/cetak_pdf', [ArticleController::class, 'cetak_pdf']);
Route::post('cari',[MahasiswaController::class,'cari']);
Route::get('mahasiswa/nilai/{nim}', [MahasiswaController::class, 'nilai'])->name('mahasiswa.nilai');
Route::resource('articles', ArticleController::class);
Route::resource('mahasiswa', MahasiswaController::class);

