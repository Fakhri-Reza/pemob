<?php

namespace App\Http\Controllers;

use App\Models\KategoriCampaign;
use Illuminate\Http\Request;

class KategoriCampaignController extends Controller
{
    // READ (Semua Data)
    public function index()
    {
        $data = KategoriCampaign::all();
        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    // CREATE (Tambah Data)
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:50'
        ]);

        $kategori = KategoriCampaign::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori berhasil ditambahkan',
            'data' => $kategori
        ]);
    }

    // READ (Detail Satu Data)
    public function show($id)
    {
        $data = KategoriCampaign::find($id);
        if($data){
            return response()->json(['status' => 'success', 'data' => $data]);
        }
        return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
    }

    // UPDATE (Edit Data)
    public function update(Request $request, $id)
    {
        $kategori = KategoriCampaign::find($id);
        if($kategori){
            $kategori->update($request->all());
            return response()->json(['status' => 'success', 'message' => 'Data berhasil diupdate', 'data' => $kategori]);
        }
        return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
    }

    // DELETE (Hapus Data)
    public function destroy($id)
    {
        $kategori = KategoriCampaign::find($id);
        if($kategori){
            $kategori->delete();
            return response()->json(['status' => 'success', 'message' => 'Data berhasil dihapus']);
        }
        return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
    }
}