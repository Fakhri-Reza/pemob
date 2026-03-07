<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KategoriCampaignController;
use App\Http\Controllers\UserController; // ← Tambahkan baris ini!

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