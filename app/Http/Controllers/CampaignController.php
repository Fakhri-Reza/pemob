<?php
// FILE: app/Http/Controllers/CampaignController.php

namespace App\Http\Controllers;  // ← Namespace HARUS sesuai lokasi folder

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CampaignController extends Controller  // ← Nama class HARUS sama dengan nama file
{
    public function index()
    {
        try {
            $campaigns = Campaign::with(['user', 'kategori'])->get();
            
            return response()->json([
                'success' => true,
                'message' => 'List Data Campaign',
                'data'    => $campaigns
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_user'        => 'required|exists:users,id_user',
                'id_kategori'    => 'required|exists:kategori_campaign,id_kategori',
                'judul_campaign' => 'required|string|max:255',
                'deskripsi'      => 'required|string',
                'target_donasi'  => 'required|numeric|min:0',
                'tanggal_mulai'  => 'required|date',
                'tanggal_selesai'=> 'required|date|after_or_equal:tanggal_mulai',
                'status'         => 'in:aktif,selesai,ditutup',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi Gagal',
                    'errors'  => $validator->errors()
                ], 400);
            }

            $input = $request->all();
            if (!isset($input['dana_terkumpul'])) {
                $input['dana_terkumpul'] = 0;
            }

            $campaign = Campaign::create($input);

            return response()->json([
                'success' => true,
                'message' => 'Campaign Berhasil Dibuat',
                'data'    => $campaign
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}