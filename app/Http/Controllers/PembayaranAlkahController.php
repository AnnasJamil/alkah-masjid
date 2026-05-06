<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PembayaranAlkah;
use App\Models\TransaksiAlkah;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\JurnalKas;
use App\Models\Alkah;

class PembayaranAlkahController
{
    //crud pembayaran alkah dari transaksi alkah api json
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'List Pembayaran Alkah',
            'data' => PembayaranAlkah::all()
        ], 200);
    }

    public function show($id)
    {
        $pembayaranAlkah = PembayaranAlkah::find($id);
        if ($pembayaranAlkah) {
            return response()->json([
                'success' => true,
                'message' => 'Detail Pembayaran Alkah',
                'data' => $pembayaranAlkah
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran Alkah tidak ditemukan',
            ], 404);
        }
    }

    //api kirim bukti pembayaran alkah dari transaksi alkah api json
    public function uploadBukti(Request $request, $id)
    {
        $pembayaranAlkah = PembayaranAlkah::find($id);
        if (!$pembayaranAlkah) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran Alkah tidak ditemukan',
            ], 404);
        }
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        // Hapus bukti pembayaran lama jika ada
        if ($pembayaranAlkah->bukti_pembayaran) {
            Storage::disk('public')->delete($pembayaranAlkah->bukti_pembayaran);
        }

        $buktiPath = $request->file('bukti_pembayaran')->store('bukti_bayar', 'public');
        $pembayaranAlkah->update(['bukti_pembayaran' => $buktiPath]);
        return response()->json([
            'success' => true,
            'message' => 'Bukti pembayaran berhasil diunggah',
            'data' => $pembayaranAlkah
        ], 200);
    }

    public function verifikasiPembayaran($id)
    {
        $pembayaranAlkah = PembayaranAlkah::find($id);
        if (!$pembayaranAlkah) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran Alkah tidak ditemukan',
            ], 404);
        }
        $pembayaranAlkah->update(['status' => 'Diverifikasi']);
        $transaksiAlkah = TransaksiAlkah::find($pembayaranAlkah->transaksi_alkah_id);
        $transaksiAlkah->update(['status' => 'Lunas']);
        $alkah = $transaksiAlkah->alkah;
        $alkah->update(['status' => 'Terisi']);

        JurnalKas::create([
            'kategori_kas_id' => 1, //kategori kas kas alkah
            'pembayaran_alkah_id' => $pembayaranAlkah->id,
            'infaq_id' => null,
            'jenis_kas' => 'Masuk',
            'tanggal' => now(),
            'keterangan' => 'Pembayaran Alkah Dengan Kode ' . $alkah->kode_alkah,
            'nominal' => $pembayaranAlkah->total_bayar,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil diverifikasi',
            'data' => $pembayaranAlkah
        ], 200);
    }
}
