<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiAlkah;
use Illuminate\Support\Str;
use App\Models\Alkah;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\PembayaranAlkah;
use Illuminate\Support\Facades\Auth;

class TransaksiAlkahController
{
    //crud transaksi alkah api json
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'List Transaksi Alkah',
            'data' => TransaksiAlkah::all()
        ], 200);
    }

    public function show($id)
    {
        $transaksiAlkah = TransaksiAlkah::find($id);
        if ($transaksiAlkah) {
            return response()->json([
                'success' => true,
                'message' => 'Detail Transaksi Alkah',
                'data' => $transaksiAlkah
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi Alkah tidak ditemukan',
            ], 404);
        }
    }

    //api transaksi alkah + otomatis buat pembayaran
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'alkah_id' => 'required|exists:alkahs,id',
            'ahli_waris' => 'required|string',
            'no_hp' => 'required|string',
            'foto_ktp' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'tanggal_pemesanan' => 'nullable|date',
            'total' => 'nullable|numeric',
            'status' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 422);
        }

        DB::transaction(function () use ($request, &$transaksi) {
            $alkah = Alkah::findOrFail($request->alkah_id);
            $ktpPath = $request->file('foto_ktp')->store('foto_ktp', 'public');
            $transaksi = TransaksiAlkah::create([
                'kode_transaksi' => 'TRX-' . str_pad(TransaksiAlkah::count() + 1, 4, '0', STR_PAD_LEFT),
                'user_id' => auth()->id(),
                'alkah_id' => $alkah->id,
                'ahli_waris' => $request->ahli_waris,
                'no_hp' => $request->no_hp,
                'foto_ktp' => $ktpPath,
                'tanggal_pemesanan' => now(),
                'total' => $alkah->harga,
                'status' => 'Pending',
            ]);

            // 🔥 otomatis buat pembayaran
            PembayaranAlkah::create([
                'transaksi_alkah_id' => $transaksi->id,
                'total_bayar' => $alkah->harga,
                'status' => 'Menunggu Pembayaran',
                'bukti_pembayaran' => null,
                'waktu_pembayaran' => now()
            ]);

        });

        return response()->json([
            'success' => true,
            'message' => 'Transaksi + pembayaran berhasil dibuat',
            'data' => $transaksi
        ], 201);
    }
}
