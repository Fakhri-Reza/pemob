<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KategoriCampaignController;
use App\Http\Controllers\UserController; // ← Tambahkan baris ini!
use App\Http\Controllers\CampaignController; 
use App\Http\Controllers\Api\DonasiController;
use App\Http\Controllers\Api\TransaksiController; // 

// Endpoint untuk Kategori (CRUD Master Data)
Route::get('/kategori', [KategoriCampaignController::class, 'index']);
Route::get('/kategori/{id}', [KategoriCampaignController::class, 'show']);
Route::post('/kategori', [KategoriCampaignController::class, 'store']);
Route::put('/kategori/{id}', [KategoriCampaignController::class, 'update']);
Route::delete('/kategori/{id}', [KategoriCampaignController::class, 'destroy']);


// Route untuk User
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

// TAMBAHKAN INI (Wajib):

Route::get('/campaigns', [CampaignController::class, 'index']);
Route::post('/campaigns', [CampaignController::class, 'store']);

// Route Donasi (BARU)
Route::get('/donasi', [DonasiController::class, 'index']);                    // Semua donasi
Route::get('/donasi/campaign/{id_campaign}', [DonasiController::class, 'byCampaign']); // Donasi per campaign
Route::get('/donasi/user/{id_user}', [DonasiController::class, 'myDonations']); // Riwayat donasi user
Route::post('/donasi', [DonasiController::class, 'store']);                  // Buat donasi baru
Route::put('/donasi/{id_donasi}/status', [DonasiController::class, 'updateStatus']); // Update status

Route::get('/transactions', [TransaksiController::class, 'index']);                    // Semua transaksi
Route::get('/transactions/user/{id_user}', [TransaksiController::class, 'userTransactions']); // Per user
Route::get('/transactions/{kode_transaksi}', [TransaksiController::class, 'show']);    // Detail by kode
Route::post('/transactions', [TransaksiController::class, 'store']);                   // Buat transaksi
Route::put('/transactions/{id_transaksi}/status', [TransaksiController::class, 'updateStatus']); // Update status
Route::delete('/transactions/{id_transaksi}', [TransaksiController::class, 'destroy']); // Batalkan