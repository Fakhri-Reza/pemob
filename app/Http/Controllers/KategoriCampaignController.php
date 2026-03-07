<?php

namespace App\Http\Controllers;

use App\Models\KategoriCampaign;
use Illuminate\Http\Request;

class KategoriCampaignController extends Controller
{
    public function index()
    {
        $data = KategoriCampaign::all();
        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function store(Request $request)
    {
        $request->validate(['nama_kategori' => 'required|string|max:50']);
        $kategori = KategoriCampaign::create($request->all());
        return response()->json(['status' => 'success', 'message' => 'Berhasil ditambahkan', 'data' => $kategori]);
    }

    public function show($id)
    {
        $data = KategoriCampaign::find($id);
        if($data) {
            return response()->json(['status' => 'success', 'data' => $data]);
        }
        return response()->json(['status' => 'error', 'message' => 'Tidak ditemukan'], 404);
    }

    public function update(Request $request, $id)
    {
        $kategori = KategoriCampaign::find($id);
        if($kategori) {
            $kategori->update($request->all());
            return response()->json(['status' => 'success', 'message' => 'Berhasil diupdate', 'data' => $kategori]);
        }
        return response()->json(['status' => 'error', 'message' => 'Tidak ditemukan'], 404);
    }

    public function destroy($id)
    {
        $kategori = KategoriCampaign::find($id);
        if($kategori) {
            $kategori->delete();
            return response()->json(['status' => 'success', 'message' => 'Berhasil dihapus']);
        }
        return response()->json(['status' => 'error', 'message' => 'Tidak ditemukan'], 404);
    }
}