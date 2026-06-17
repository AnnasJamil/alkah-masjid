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
            'data' => Infaq::with('targetInfaq')->get()
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
            'data' => Infaq::with('targetInfaq')->find($id)
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_penginfaq' => 'nullable|string|max:255',
            'tujuan_infaq' => 'required_without:target_infaq_id|string',
            'target_infaq_id' => 'required_without:tujuan_infaq|exists:target_infaqs,id',
            'nominal' => 'required|numeric|min:1000',
            'bukti_infaq' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $buktiInfaq = $request->file('bukti_infaq')
            ->store('bukti_infaq', 'public');

        $infaq = Infaq::create([
            'nama_penginfaq' => $request->nama_penginfaq ?: 'Hamba Allah',
            'target_infaq_id' => $request->target_infaq_id,
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
    $request->validate([
        'nominal' => 'nullable|numeric|min:1000'
    ]);

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

    $nominalFinal = $request->nominal ?? $infaq->nominal;

    $infaq->update([
        'nominal' => $nominalFinal,
        'status' => 'Diterima'
    ]);

    $tujuan = $infaq->target_infaq_id
    ? $infaq->targetInfaq->nama_target
    : $infaq->tujuan_infaq;

    JurnalKas::create([
        'pembayaran_alkah_id' => null,
        'infaq_id' => $infaq->id,
        'jenis_kas' => 'Masuk',
        'tanggal' => now(),
        'keterangan' =>
            'Infaq ' .
            $tujuan .
            ' dari ' .
            $infaq->nama_penginfaq,
        'nominal' => $nominalFinal,
    ]);

    if ($request->user()) {
        LogAktivitas::create([
            'user_id' => $request->user()->id,
            'aktivitas' =>
                'Menerima Infaq sebesar Rp ' .
                number_format($nominalFinal, 0, ',', '.'),
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

        //log aktivitas
        if (auth()->check()) {
            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Menolak Infaq sebesar ' . $infaq->nominal,
                'waktu' => now(),
            ]);
        }

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

        //log aktivitas
        if (auth()->check()) {
            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Menghapus Infaq sebesar ' . $infaq->nominal,
                'waktu' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Infaq berhasil dihapus'
        ], 200);
    }
}
