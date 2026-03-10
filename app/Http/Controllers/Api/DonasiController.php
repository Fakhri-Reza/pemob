<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donasi;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class DonasiController extends Controller
{
    /**
     * GET: List semua donasi
     */
    public function index()
    {
        $donasis = Donasi::with(['user', 'campaign'])->get();
        
        return response()->json([
            'success' => true,
            'message' => 'List Data Donasi',
            'data'    => $donasis
        ], 200);
    }

    /**
     * GET: Donasi per campaign
     */
    public function byCampaign($id_campaign)
    {
        $donasis = Donasi::with('user')
            ->where('id_campaign', $id_campaign)
            ->where('status_pembayaran', 'berhasil')
            ->get();
        
        $total = $donasis->sum('jumlah_donasi');

        return response()->json([
            'success' => true,
            'message' => 'Donasi per Campaign',
            'data'    => [
                'id_campaign' => $id_campaign,
                'total_donasi' => $total,
                'jumlah_donatur' => $donasis->count(),
                'list_donasi' => $donasis
            ]
        ], 200);
    }

    /**
     * POST: Buat donasi baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user'          => 'required|exists:users,id_user',
            'id_campaign'      => 'required|exists:campaign,id_campaign',
            'jumlah_donasi'    => 'required|numeric|min:1000', // Minimal donasi 1000
            'pesan_donasi'     => 'nullable|string|max:500',
            'metode_pembayaran'=> 'required|in:transfer,qris,ovo,gopay,dana',
            'bukti_pembayaran' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors'  => $validator->errors()
            ], 400);
        }

        try {
            DB::beginTransaction();

            $input = $request->all();

            // Generate kode unik untuk pembayaran (contoh: 123 -> 123587)
            $kodeUnik = rand(100, 999);
            $input['kode_unik'] = $kodeUnik;

            // Handle upload bukti pembayaran
            if ($request->hasFile('bukti_pembayaran')) {
                $image = $request->file('bukti_pembayaran');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('public/bukti_donasi', $imageName);
                $input['bukti_pembayaran'] = $imageName;
            }

            // Set status default
            $input['status_pembayaran'] = 'pending';

            $donasi = Donasi::create($input);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Donasi Berhasil Dibuat',
                'data'    => $donasi,
                'info_pembayaran' => [
                    'total_bayar' => $input['jumlah_donasi'],
                    'kode_unik' => $kodeUnik,
                    'total_dengan_unik' => $input['jumlah_donasi'] + $kodeUnik
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * PUT: Update status pembayaran (untuk admin/verifikasi)
     */
    public function updateStatus(Request $request, $id_donasi)
    {
        $validator = Validator::make($request->all(), [
            'status_pembayaran' => 'required|in:pending,berhasil,gagal'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors'  => $validator->errors()
            ], 400);
        }

        $donasi = Donasi::find($id_donasi);

        if (!$donasi) {
            return response()->json([
                'success' => false,
                'message' => 'Data donasi tidak ditemukan'
            ], 404);
        }

        // Jika status berubah jadi berhasil, update dana_terkumpul di campaign
        if ($request->status_pembayaran == 'berhasil' && $donasi->status_pembayaran != 'berhasil') {
            $campaign = Campaign::find($donasi->id_campaign);
            if ($campaign) {
                $campaign->dana_terkumpul += $donasi->jumlah_donasi;
                $campaign->save();
            }
        }

        $donasi->status_pembayaran = $request->status_pembayaran;
        $donasi->save();

        return response()->json([
            'success' => true,
            'message' => 'Status pembayaran berhasil diupdate',
            'data'    => $donasi
        ], 200);
    }

    /**
     * GET: Riwayat donasi user
     */
    public function myDonations($id_user)
    {
        $donasis = Donasi::with('campaign')
            ->where('id_user', $id_user)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat Donasi User',
            'data'    => $donasis
        ], 200);
    }
}