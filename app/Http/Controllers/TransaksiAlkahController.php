<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiAlkah;
use App\Models\Alkah;
use App\Models\PembayaranAlkah;
use App\Models\ProfilJamaah;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TransaksiAlkahController
{
    // =====================================
    // LIST TRANSAKSI
    // =====================================

    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'List Transaksi Alkah',

            'data' => TransaksiAlkah::with(
                'user',
                'alkah',
                'pembayaranAlkah'
            )->get()

        ], 200);
    }

    // =====================================
    // DETAIL TRANSAKSI
    // =====================================

    public function show($id)
    {
        $transaksiAlkah = TransaksiAlkah::with(
            'user',
            'alkah',
            'pembayaranAlkah'
        )->find($id);

        if (!$transaksiAlkah) {

            return response()->json([
                'success' => false,
                'message' => 'Transaksi Alkah tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail Transaksi Alkah',
            'data' => $transaksiAlkah
        ], 200);
    }

    // =====================================
    // TRANSAKSI ALKAH
    // =====================================

    public function store(Request $request)
    {
        // =====================================
        // VALIDASI
        // =====================================

        $validator = Validator::make($request->all(), [

            'alkah_id' => 'required|exists:alkahs,id',

        ]);

        if ($validator->fails()) {

            return response()->json([

                'success' => false,

                'message' => $validator->errors()

            ], 422);
        }

        // =====================================
        // CEK PROFIL USER
        // =====================================

        $profil = ProfilJamaah::where(
            'user_id',
            auth()->id()
        )->first();

        if (!$profil) {

            return response()->json([

                'success' => false,

                'message' =>
                'Lengkapi profil terlebih dahulu sebelum membeli alkah'

            ], 400);
        }

        // =====================================
        // TRANSACTION DATABASE
        // =====================================

        DB::transaction(function () use ($request, &$transaksi) {

            // ambil data alkah
            $alkah = Alkah::findOrFail($request->alkah_id);

            // =====================================
            // CEK STATUS ALKAH
            // =====================================

            if ($alkah->status == 'Terisi') {

                return response()->json([

                    'success' => false,

                    'message' => 'Alkah sudah terisi'

                ], 400);
            }

            // =====================================
            // BUAT TRANSAKSI
            // =====================================

            $transaksi = TransaksiAlkah::create([

                'kode_transaksi' =>
                'TRX-' .
                str_pad(
                    TransaksiAlkah::count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                ),

                'user_id' => auth()->id(),

                'alkah_id' => $alkah->id,

                'tanggal_pemesanan' => now(),

                'total' => $alkah->harga,

                'status' => 'Pending',
            ]);

            // =====================================
            // BUAT PEMBAYARAN
            // =====================================

            PembayaranAlkah::create([

                'transaksi_alkah_id' => $transaksi->id,

                'total_bayar' => $alkah->harga,

                'status' => 'Menunggu Pembayaran',

                'bukti_pembayaran' => null,
            ]);
        });

        // =====================================
        // RESPONSE
        // =====================================

        return response()->json([

            'success' => true,

            'message' =>
            'Transaksi dan pembayaran berhasil dibuat',

            'data' => $transaksi

        ], 201);
    }
}
