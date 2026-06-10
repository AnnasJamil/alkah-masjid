<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlokAlkahController;
use App\Http\Controllers\AlkahController;
use App\Http\Controllers\TransaksiAlkahController;
use App\Http\Controllers\PembayaranAlkahController;
use App\Http\Controllers\InfaqController;
use App\Http\Controllers\JurnalKasController;
use App\Http\Controllers\DaftarTPAController;
use App\Http\Controllers\StrukturPengurusController;
use App\Http\Controllers\KategoriKontenController;
use App\Http\Controllers\KontenWebController;
use App\Http\Controllers\LogAktivitasController;
use App\Http\Controllers\ProfiljamaahController;
use App\Http\Controllers\DataAlmarhumController;
use App\Http\Controllers\KajianRutinController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//register, login, logout
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/update-password', [AuthController::class, 'updatePassword']);
Route::post('/profil', [ProfilJamaahController::class, 'store'])->middleware('auth:sanctum');
Route::get('/profil', [ProfilJamaahController::class, 'show'])->middleware('auth:sanctum');

//crud blok alkah api json
Route::get('/blok', [BlokAlkahController::class, 'index']);
Route::get('/blok/{id}', [BlokAlkahController::class, 'show']);
Route::post('/blok', [BlokAlkahController::class, 'store'])->middleware('auth:sanctum');
Route::put('/blok/{id}', [BlokAlkahController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/blok/{id}', [BlokAlkahController::class, 'destroy'])->middleware('auth:sanctum');

//crud alkah api json
Route::get('/alkah', [AlkahController::class, 'index']);
Route::get('/alkah/{id}', [AlkahController::class, 'show']);
Route::post('/alkah', [AlkahController::class, 'store'])->middleware('auth:sanctum');
Route::put('/alkah/{id}', [AlkahController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/alkah/{id}', [AlkahController::class, 'destroy'])->middleware('auth:sanctum');

//crud transaksi alkah api json
Route::get('/transaksi', [TransaksiAlkahController::class, 'index']);
Route::get('/transaksi/{id}', [TransaksiAlkahController::class, 'show']);
Route::post('/transaksi', [TransaksiAlkahController::class, 'store'])->middleware('auth:sanctum', 'role:Jamaah');
Route::put('/transaksi/{id}/terima', [TransaksiAlkahController::class, 'terimaPengajuan'])->middleware('auth:sanctum');
Route::put('/transaksi/{id}/tolak', [TransaksiAlkahController::class, 'tolakPengajuan'])->middleware('auth:sanctum');

//crud pembayaran alkah api json
Route::get('/pembayaran', [PembayaranAlkahController::class, 'index']);
Route::get('/pembayaran/{id}', [PembayaranAlkahController::class, 'show']);
Route::post('/upload-bukti/{id}', [PembayaranAlkahController::class, 'uploadBukti'])->middleware('auth:sanctum');
Route::post('/perbaiki-bukti/{id}', [PembayaranAlkahController::class, 'perbaikiBukti'])->middleware('auth:sanctum');
Route::post('/verifikasi-pembayaran/{id}', [PembayaranAlkahController::class, 'verifikasiPembayaran'])->middleware('auth:sanctum');

//crud jurnal kas api json
Route::get('/jurnal-kas', [JurnalKasController::class, 'index']);
Route::get('/jurnal-kas/{id}', [JurnalKasController::class, 'show']);
Route::post('/jurnal-kas', [JurnalKasController::class, 'store'])->middleware('auth:sanctum');
Route::put('/jurnal-kas/{id}', [JurnalKasController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/jurnal-kas/{id}', [JurnalKasController::class, 'destroy'])->middleware('auth:sanctum');
Route::get('/jurnal-minggu', [JurnalKasController::class, 'LaporanMingguan']);

//crud infaq api json
Route::get('/infaq', [InfaqController::class, 'index']);
Route::get('/infaq/{id}', [InfaqController::class, 'show']);
Route::post('/infaq', [InfaqController::class, 'store']);
Route::post('/terima-infaq/{id}', [InfaqController::class, 'terimaInfaq'])->middleware('auth:sanctum');

//crud Pendaftaran TPA api json
Route::get('/daftar-tpa', [DaftarTPAController::class, 'index']);
Route::get('/daftar-tpa/{id}', [DaftarTPAController::class, 'show']);
Route::post('/daftar-tpa', [DaftarTPAController::class, 'store']);
Route::put('/daftar-tpa/{id}', [DaftarTPAController::class, 'update']);
Route::delete('/daftar-tpa/{id}', [DaftarTPAController::class, 'destroy']);

//crud struktur pengurus api json
Route::get('/pengurus', [StrukturPengurusController::class, 'index']);
Route::get('/pengurus/{id}', [StrukturPengurusController::class, 'show']);
Route::post('/pengurus', [StrukturPengurusController::class, 'store']);
Route::put('/pengurus/{id}', [StrukturPengurusController::class, 'update']);
Route::delete('/pengurus/{id}', [StrukturPengurusController::class, 'destroy']);

//crud kategori konten api json
Route::get('/kategori-konten', [KategoriKontenController::class, 'index']);
Route::get('/kategori-konten/{id}', [KategoriKontenController::class, 'show']);
Route::post('/kategori-konten', [KategoriKontenController::class, 'store']);
Route::put('/kategori-konten/{id}', [KategoriKontenController::class, 'update']);
Route::delete('/kategori-konten/{id}', [KategoriKontenController::class, 'destroy']);

//crud konten web api json
Route::get('/web', [KontenWebController::class, 'index']);
Route::get('/web/{id}', [KontenWebController::class, 'show'])->where('id', '[0-9]+');
Route::post('/web', [KontenWebController::class, 'store'])->middleware('auth:sanctum');
Route::put('/web/{id}', [KontenWebController::class, 'update']);
Route::delete('/web/{id}', [KontenWebController::class, 'destroy']);
Route::get('/web/published', [KontenWebController::class, 'published']);

//crud data almarhum api json
Route::get('/almarhum', [DataAlmarhumController::class, 'index']);
Route::get('/almarhum/{id}', [DataAlmarhumController::class, 'show']);
Route::post('/almarhum', [DataAlmarhumController::class, 'store']);
Route::put('/almarhum/{id}', [DataAlmarhumController::class, 'update']);
Route::delete('/almarhum/{id}', [DataAlmarhumController::class, 'destroy']);

//crud kajian rutin api json
Route::get('/kajian', [KajianRutinController::class, 'index']);
Route::get('/kajian/{id}', [KajianRutinController::class, 'show']);
Route::post('/kajian', [KajianRutinController::class, 'store']);
Route::put('/kajian/{id}', [KajianRutinController::class, 'update']);
Route::delete('/kajian/{id}', [KajianRutinController::class, 'destroy']);

//log aktivitas api json
Route::get('/log', [LogAktivitasController::class, 'index']);
