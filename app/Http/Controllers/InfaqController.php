<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Infaq;
use App\Models\JurnalKas;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InfaqController
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'List Infaq',
            'data' => Infaq::all()
        ], 200);
    }

    public function show($id)
    {
        $infaq = Infaq::find($id);

        if (!$infaq) {
            return response()->json([
                'success' => false,
                'message' => 'Infaq tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail Infaq',
            'data' => $infaq
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_penginfaq' => 'nullable|string|max:255',
            'tujuan_infaq' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:1000',
            'bukti_infaq' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $buktiInfaq = $request->file('bukti_infaq')
            ->store('bukti_infaq', 'public');

        $infaq = Infaq::create([
            'nama_penginfaq' => $request->nama_penginfaq ?: 'Hamba Allah',

            'tujuan_infaq' => $request->tujuan_infaq,

            'nominal' => $request->nominal,

            'bukti_infaq' => $buktiInfaq,

            'status' => 'Menunggu Diterima',

            'tanggal_infaq' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Infaq berhasil disimpan',
            'data' => $infaq
        ], 201);
    }

    // =====================================
    // TERIMA INFAQ
    // =====================================

    public function terimaInfaq(Request $request, $id)
    {
        $infaq = Infaq::find($id);

        if (!$infaq) {
            return response()->json([
                'success' => false,
                'message' => 'Infaq tidak ditemukan'
            ], 404);
        }

        if ($infaq->status == 'Diterima') {
            return response()->json([
                'success' => false,
                'message' => 'Infaq sudah diverifikasi sebelumnya'
            ], 400);
        }

        DB::transaction(function () use ($infaq, $request) {

            $infaq->update([
                'status' => 'Diterima'
            ]);

            JurnalKas::create([
                'pembayaran_alkah_id' => null,
                'infaq_id' => $infaq->id,
                'jenis_kas' => 'Masuk',
                'tanggal' => now(),
                'keterangan' => 'Infaq ' . $infaq->tujuan_infaq .  ' dari ' .  $infaq->nama_penginfaq,
                'nominal' => $infaq->nominal,
            ]);

            if ($request->user()) {
                LogAktivitas::create([
                    'user_id' => $request->user()->id,
                    'aktivitas' => 'Menerima Infaq sebesar Rp ' .  number_format($infaq->nominal, 0, ',', '.'),
                    'waktu' => now(),
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Infaq berhasil diterima dan masuk ke jurnal kas',
            'data' => $infaq->fresh()
        ], 200);
    }

    // =====================================
    // TOLAK INFAQ
    // =====================================

    public function tolakInfaq($id)
    {
        $infaq = Infaq::find($id);

        if (!$infaq) {
            return response()->json([
                'success' => false,
                'message' => 'Infaq tidak ditemukan'
            ], 404);
        }

        $infaq->update([
            'status' => 'Ditolak'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Infaq berhasil ditolak',
            'data' => $infaq
        ], 200);
    }

    // =====================================
    // HAPUS INFAQ
    // =====================================

    public function destroy($id)
    {
        $infaq = Infaq::find($id);

        if (!$infaq) {
            return response()->json([
                'success' => false,
                'message' => 'Infaq tidak ditemukan'
            ], 404);
        }

        if ($infaq->bukti_infaq) {
            Storage::disk('public')
                ->delete($infaq->bukti_infaq);
        }

        $infaq->delete();

        return response()->json([
            'success' => true,
            'message' => 'Infaq berhasil dihapus'
        ], 200);
    }
}
