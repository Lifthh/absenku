<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Guru\AbsensiController as GuruAbsensiController;
use App\Http\Controllers\Guru\RiwayatController as GuruRiwayatController;
use App\Http\Controllers\Guru\IzinController as GuruIzinController;
use App\Http\Controllers\Guru\ProfilController as GuruProfilController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\GuruController as AdminGuruController;
use App\Http\Controllers\Admin\AbsensiController as AdminAbsensiController;
use App\Http\Controllers\Admin\IzinController as AdminIzinController;
use App\Http\Controllers\Admin\LokasiController as AdminLokasiController;
use App\Http\Controllers\Admin\JamKerjaController as AdminJamKerjaController;
use App\Http\Controllers\Admin\HariLiburController as AdminHariLiburController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporanController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});


/*
|--------------------------------------------------------------------------
| Redirect utama setelah login & session refresh (berdasarkan role)
|--------------------------------------------------------------------------
*/
Route::get('/home', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    if (auth()->user()->role === 'guru') {
        return redirect()->route('guru.dashboard');
    }

    abort(403);
});


/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Guru Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');
    
    // Absensi
    Route::get('/absensi', [GuruAbsensiController::class, 'index'])->name('absensi.index');
    Route::post('/absensi', [GuruAbsensiController::class, 'store'])->name('absensi.store');
    Route::post('/absensi/cek-lokasi', [GuruAbsensiController::class, 'cekLokasi'])->name('absensi.cek-lokasi');
    
    // Riwayat
    Route::get('/riwayat', [GuruRiwayatController::class, 'index'])->name('riwayat.index');
    
    // Izin
    Route::get('/izin', [GuruIzinController::class, 'index'])->name('izin.index');
    Route::get('/izin/create', [GuruIzinController::class, 'create'])->name('izin.create');
    Route::post('/izin', [GuruIzinController::class, 'store'])->name('izin.store');
    Route::get('/izin/{id}', [GuruIzinController::class, 'show'])->name('izin.show');
    
    // Profil
    Route::get('/profil', [GuruProfilController::class, 'index'])->name('profil.index');
    Route::put('/profil', [GuruProfilController::class, 'update'])->name('profil.update');
    Route::put('/profil/password', [GuruProfilController::class, 'updatePassword'])->name('profil.password');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Kelola Guru
    Route::resource('guru', AdminGuruController::class);
    
    // Absensi
    Route::get('/absensi', [AdminAbsensiController::class, 'index'])->name('absensi.index');
    Route::get('/absensi/{id}', [AdminAbsensiController::class, 'show'])->name('absensi.show');
    
    // Izin
    Route::get('/izin', [AdminIzinController::class, 'index'])->name('izin.index');
    Route::get('/izin/{id}', [AdminIzinController::class, 'show'])->name('izin.show');
    Route::post('/izin/{id}/approve', [AdminIzinController::class, 'approve'])->name('izin.approve');
    
    // Lokasi
    Route::get('/lokasi', [AdminLokasiController::class, 'index'])->name('lokasi.index');
    Route::put('/lokasi', [AdminLokasiController::class, 'update'])->name('lokasi.update');
    
    // Jam Kerja
    Route::get('/jam-kerja', [AdminJamKerjaController::class, 'index'])->name('jam-kerja.index');
    Route::put('/jam-kerja', [AdminJamKerjaController::class, 'update'])->name('jam-kerja.update');
    
    // Hari Libur
    Route::get('/hari-libur', [AdminHariLiburController::class, 'index'])->name('hari-libur.index');
    Route::post('/hari-libur', [AdminHariLiburController::class, 'store'])->name('hari-libur.store');
    Route::delete('/hari-libur/{id}', [AdminHariLiburController::class, 'destroy'])->name('hari-libur.destroy');
    
    // Laporan
    Route::get('/laporan', [AdminLaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export-excel', [AdminLaporanController::class, 'exportExcel'])->name('laporan.export-excel');
    Route::get('/laporan/export-pdf', [AdminLaporanController::class, 'exportPdf'])->name('laporan.export-pdf');
});