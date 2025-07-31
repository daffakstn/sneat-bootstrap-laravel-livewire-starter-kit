<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Users\Users;
use App\Livewire\Prodi\Prodi;
use App\Livewire\Jabatan\Jabatan;
use App\Livewire\Tahun\Tahun;
use App\Livewire\LembagaAkreditasi\LembagaAkreditasi;
use App\Livewire\StandarNasional\StandarNasional;
use App\Livewire\StandarNasional\Level2StandarNasional;
use App\Livewire\StandarNasional\Level3StandarNasional;
use App\Livewire\StandarMutu\StandarMutu;
use App\Livewire\StandarMutu\SubStandarMutu;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('/users', Users::class)->name('users')->middleware(['auth', 'verified']);
    Route::get('/prodi', Prodi::class)->name('prodi')->middleware(['auth', 'verified']);
    Route::get('/jabatan', Jabatan::class)->name('jabatan')->middleware(['auth', 'verified']);
    Route::get('/tahun', Tahun::class)->name('tahun')->middleware(['auth', 'verified']);
    Route::get('/lembaga-akreditasi', LembagaAkreditasi::class)->name('lembaga-akreditasi')->middleware(['auth', 'verified']);
    
    // Standar Nasional routes - Fixed parameter name
    Route::get('/standar-nasional', StandarNasional::class)->name('standar-nasional')->middleware(['auth', 'verified']);
    Route::get('/standar-nasional/{parentId}/level2', Level2StandarNasional::class)->name('standar-nasional.level2')->middleware(['auth', 'verified']);
    // Changed {level2Id} to {parentId} to match the component parameter
    Route::get('/standar-nasional/level3/{parentId}', Level3StandarNasional::class)->name('standar-nasional.level3')->middleware(['auth', 'verified']);
    
    Route::get('/standar-mutu', StandarMutu::class)->name('standar-mutu')->middleware(['auth', 'verified']);
    Route::get('/standar-mutu/{standarMutu}/sub-standar', SubStandarMutu::class)->name('standar-mutu.sub-standar')->middleware(['auth', 'verified']);
    
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
});

require __DIR__ . '/auth.php';