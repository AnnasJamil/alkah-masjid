<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PembayaranAlkah;
use App\Models\TransaksiAlkah;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\JurnalKas;
use App\Models\Alkah;
use App\Models\BlokAlkah;
use App\Models\LogAktivitas;

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
    $pembayaran = PembayaranAlkah::find($id);

    if (!$pembayaran) {
        return response()->json([
            'success' => false,
            'message' => 'Pembayaran tidak ditemukan'
        ], 404);
    }

    $request->validate([
        'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    if ($pembayaran->bukti_pembayaran) {
        Storage::disk('public')
            ->delete($pembayaran->bukti_pembayaran);
    }

    $path = $request->file('bukti_pembayaran')
        ->store('bukti_bayar', 'public');

    $pembayaran->update([
        'bukti_pembayaran' => $path,
        'status' => 'Menunggu Verifikasi',
        'catatan' => null
    ]);

    $alkah = Alkah::find($pembayaran->transaksiAlkah->alkah_id);
    //log aktivitas upload bukti pembayaran
    LogAktivitas::create([
        'user_id' => auth()->id(),
        'aktivitas' =>
            'Mengupload bukti pembayaran untuk transaksi alkah ' . $alkah->kode_alkah,
        'waktu' => now()
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Bukti pembayaran berhasil diupload',
        'data' => $pembayaran
    ]);
    }

    //perbaiki butki
    public function perbaikiBukti(Request $request, $id)
    {
    $request->validate([
        'catatan' => 'required|string'
    ]);

    $pembayaran = PembayaranAlkah::find($id);

    if (!$pembayaran) {
        return response()->json([
            'success' => false,
            'message' => 'Pembayaran tidak ditemukan'
        ], 404);
    }

    $pembayaran->update([
        'status' => 'Menunggu Pembayaran',
        'catatan' => $request->catatan
    ]);

    $alkah = Alkah::find($pembayaran->transaksiAlkah->alkah_id);
    //log aktivitas perbaiki bukti pembayaran
    LogAktivitas::create([
        'user_id' => auth()->id(),
        'aktivitas' =>
            'Memperbaiki bukti pembayaran untuk transaksi alkah ' . $alkah->kode_alkah,
        'waktu' => now()
    ]);
    return response()->json([
        'success' => true,
        'message' => 'Bukti pembayaran perlu diperbaiki',
        'data' => $pembayaran
    ]);
    }

    public function verifikasiPembayaran($id)
    {
    $pembayaran = PembayaranAlkah::find($id);

    if (!$pembayaran) {
        return response()->json([
            'success' => false,
            'message' => 'Pembayaran tidak ditemukan'
        ], 404);
    }

    $pembayaran->update([
        'status' => 'Diverifikasi',
        'catatan' => null
    ]);

    $transaksi = TransaksiAlkah::find(
        $pembayaran->transaksi_alkah_id
    );

    $transaksi->update([
        'status' => 'Lunas'
    ]);

    $alkah = $transaksi->alkah;

    $alkah->update([
        'status' => 'Dipesan'
    ]);

    $this->cekStatusBlok(
        $alkah->blok_alkah_id
    );

    JurnalKas::create([
        'pembayaran_alkah_id' => $pembayaran->id,
        'infaq_id' => null,
        'jenis_kas' => 'Masuk',
        'tanggal' => now(),
        'keterangan' =>
            'Pembayaran Alkah Dengan Kode ' .
            $alkah->kode_alkah,
        'nominal' => $pembayaran->total_bayar,
    ]);

    $alkah = Alkah::find($transaksi->alkah_id);
    //log aktivitas verifikasi pembayaran
    LogAktivitas::create([
        'user_id' => auth()->id(),
        'aktivitas' =>
            'Memverifikasi pembayaran untuk transaksi alkah ' . $alkah->kode_alkah,
        'waktu' => now()
    ]);
    
    return response()->json([
        'success' => true,
        'message' => 'Pembayaran berhasil diverifikasi',
        'data' => $pembayaran
    ]);
    }

    //cek status
    private function cekStatusBlok($blokId)
{
    $masihAdaTersedia = Alkah::where(
        'blok_alkah_id',
        $blokId
    )
    ->where('status', 'Tersedia')
    ->exists();

    if ($masihAdaTersedia) {

        BlokAlkah::where('id', $blokId)
            ->update([
                'status' => 'Tersedia'
            ]);

    } else {

        BlokAlkah::where('id', $blokId)
            ->update([
                'status' => 'Penuh'
            ]);
    }
}
}
