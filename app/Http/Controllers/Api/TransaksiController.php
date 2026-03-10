<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    /**
     * GET: Riwayat semua transaksi
     */
    public function index()
    {
        $transaksis = Transaksi::with(['user', 'campaign'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'message' => 'List Riwayat Transaksi',
            'data'    => $transaksis
        ], 200);
    }

    /**
     * GET: Transaksi per user
     */
    public function userTransactions($id_user)
    {
        $transaksis = Transaksi::with('campaign')
            ->where('id_user', $id_user)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $total_donasi = $transaksis->where('status', 'berhasil')->sum('jumlah_donasi');

        return response()->json([
            'success' => true,
            'message' => 'Riwayat Transaksi User',
            'data'    => [
                'id_user' => $id_user,
                'total_transaksi' => $transaksis->count(),
                'total_donasi' => $total_donasi,
                'list_transaksi' => $transaksis
            ]
        ], 200);
    }

    /**
     * GET: Detail transaksi by kode
     */
    public function show($kode_transaksi)
    {
        $transaksi = Transaksi::with(['user', 'campaign'])
            ->where('kode_transaksi', $kode_transaksi)
            ->first();
        
        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail Transaksi',
            'data'    => $transaksi
        ], 200);
    }

    /**
     * POST: Buat transaksi baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user'          => 'required|exists:users,id_user',
            'id_campaign'      => 'required|exists:campaign,id_campaign',
            'jumlah_donasi'    => 'required|numeric|min:1000',
            'biaya_admin'      => 'nullable|numeric|min:0',
            'metode_pembayaran'=> 'required|in:transfer,qris,ovo,gopay,dana,shopeepay',
            'catatan'          => 'nullable|string|max:500',
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

            // Generate kode transaksi unik (INV-YYYYMMDD-XXXX)
            $date = Carbon::now()->format('Ymd');
            $random = strtoupper(substr(uniqid(), -4));
            $input['kode_transaksi'] = 'INV-' . $date . '-' . $random;

            // Hitung biaya admin jika tidak dikirim (default 2.5%)
            if (!isset($input['biaya_admin'])) {
                $input['biaya_admin'] = $input['jumlah_donasi'] * 0.025;
            }

            // Hitung total bayar
            $input['total_bayar'] = $input['jumlah_donasi'] + $input['biaya_admin'];

            // Handle upload bukti pembayaran
            if ($request->hasFile('bukti_pembayaran')) {
                $image = $request->file('bukti_pembayaran');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->storeAs('public/bukti_transaksi', $imageName);
                $input['bukti_pembayaran'] = $imageName;
            }

            // Set status default
            $input['status'] = 'pending';

            $transaksi = Transaksi::create($input);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi Berhasil Dibuat',
                'data'    => $transaksi,
                'info_pembayaran' => [
                    'kode_transaksi' => $input['kode_transaksi'],
                    'jumlah_donasi' => $input['jumlah_donasi'],
                    'biaya_admin' => $input['biaya_admin'],
                    'total_bayar' => $input['total_bayar'],
                    'tanggal_kadaluarsa' => Carbon::now()->addHours(24)->format('Y-m-d H:i:s')
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
     * PUT: Update status transaksi (Verifikasi Admin)
     */
    public function updateStatus(Request $request, $id_transaksi)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,berhasil,gagal,kadaluarsa'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi Gagal',
                'errors'  => $validator->errors()
            ], 400);
        }

        $transaksi = Transaksi::find($id_transaksi);

        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        // Jika status berubah jadi berhasil, update dana_terkumpul di campaign
        if ($request->status == 'berhasil' && $transaksi->status != 'berhasil') {
            $campaign = Campaign::find($transaksi->id_campaign);
            if ($campaign) {
                $campaign->dana_terkumpul += $transaksi->jumlah_donasi;
                $campaign->save();
            }
            
            // Set tanggal bayar
            $transaksi->tanggal_bayar = Carbon::now();
        }

        $transaksi->status = $request->status;
        $transaksi->save();

        return response()->json([
            'success' => true,
            'message' => 'Status transaksi berhasil diupdate',
            'data'    => $transaksi
        ], 200);
    }

    /**
     * DELETE: Batalkan transaksi
     */
    public function destroy($id_transaksi)
    {
        $transaksi = Transaksi::find($id_transaksi);

        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        // Hanya bisa delete jika status masih pending
        if ($transaksi->status != 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat membatalkan transaksi yang sudah diproses'
            ], 400);
        }

        $transaksi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dibatalkan'
        ], 200);
    }
}