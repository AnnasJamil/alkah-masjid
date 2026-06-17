<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiAlkah;
use App\Models\Alkah;
use App\Models\PembayaranAlkah;
use App\Models\ProfilJamaah;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class TransaksiAlkahController
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'List Transaksi Alkah',
            'data' => TransaksiAlkah::with(
                'user.profilJamaah',
                'alkah',
                'pembayaranAlkah'
            )->get()
        ], 200);
    }

    public function show($id)
    {
        $transaksiAlkah = TransaksiAlkah::with(
            'user.profilJamaah',
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

    public function store(Request $request)
    {
        // VALIDASI
        $validator = Validator::make($request->all(), [
            'alkah_id' => 'required|exists:alkahs,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 422);
        }

        // CEK PROFIL JAMAAH
        $profil = ProfilJamaah::where('user_id', auth()->id())->first();

        if (!$profil) {
            return response()->json([
                'success' => false,
                'message' => 'Lengkapi profil terlebih dahulu sebelum membeli alkah'
            ], 400);
        }

        // CEK ALKAH
        $alkah = Alkah::findOrFail($request->alkah_id);

        if ($alkah->status != 'Tersedia') {
            return response()->json([
                'success' => false,
                'message' => 'Alkah tidak tersedia'
            ], 400);
        }

        // CEK PENGAJUAN AKTIF USER
        $cekPengajuanAktif = TransaksiAlkah::where('user_id', auth()->id())
            ->whereIn('status', ['Menunggu Verifikasi', 'Menunggu Pembayaran'])
            ->exists();

        if ($cekPengajuanAktif) {
            return response()->json([
                'success' => false,
                'message' => 'Anda masih memiliki pengajuan alkah yang aktif'
            ], 400);
        }

        // CEK DUPLIKAT ALKAH
        $cekDuplikat = TransaksiAlkah::where('user_id', auth()->id())
            ->where('alkah_id', $request->alkah_id)
            ->exists();

        if ($cekDuplikat) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah pernah mengajukan alkah ini'
            ], 400);
        }

        // LOG AKTIVITAS
        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Mengajukan pembelian alkah ' . $alkah->kode_alkah,
            'waktu' => now(),
        ]);

        // BUAT TRANSAKSI
        $transaksi = TransaksiAlkah::create([
            'kode_transaksi' => 'TRX-' . str_pad(TransaksiAlkah::count() + 1, 4, '0', STR_PAD_LEFT),
            'user_id' => auth()->id(),
            'alkah_id' => $alkah->id,
            'tanggal_pemesanan' => now(),
            'total' => $alkah->harga,
            'status' => 'Menunggu Verifikasi',
            'alasan_penolakan' => null,
        ]);

        // UPDATE STATUS ALKAH
        $alkah->update(['status' => 'Sedang Dipesan']);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan alkah berhasil dikirim',
            'data' => $transaksi
        ], 201);
    }

    public function terimaPengajuan($id)
    {
        $transaksi = TransaksiAlkah::find($id);

        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        $transaksi->update([
            'status' => 'Menunggu Pembayaran',
            'alasan_penolakan' => null
        ]);

        PembayaranAlkah::create([
            'transaksi_alkah_id' => $transaksi->id,
            'total_bayar' => $transaksi->total,
            'status' => 'Menunggu Pembayaran',
            'catatan_verifikasi' => null
        ]);

        $alkah = Alkah::find($transaksi->alkah_id);

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Menerima pengajuan alkah ' . $alkah->kode_alkah,
            'waktu' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan diterima'
        ]);
    }

    public function tolakPengajuan(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string'
        ]);

        $transaksi = TransaksiAlkah::find($id);

        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        $transaksi->update([
            'status' => 'Ditolak',
            'alasan_penolakan' => $request->alasan_penolakan
        ]);

        $alkah = Alkah::find($transaksi->alkah_id);
        $alkah->update(['status' => 'Tersedia']);

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Menolak pengajuan alkah ' . $alkah->kode_alkah,
            'waktu' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan ditolak'
        ]);
    }
}
