<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Infaq;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\JurnalKas;

class InfaqController
{
    //crud api json untuk infaq
    public function index()
    {
        return response()->json([
                'success' => true,
                'message' => 'List Infaq',
                'data' => Infaq::all()
            ], 200);
    }

    public function show ($id)
    {
        $infaq = Infaq::find($id);
        if ($infaq) {
            return response()->json([
                'success' => true,
                'message' => 'Detail Infaq',
                'data' => $infaq
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Infaq tidak ditemukan',
            ], 404);
        }
    }

    //store data infaq
    // bayar bukti infaq berupa gambar di strorage/app/public/bukti_infaq
    public function store(Request $request)
    {
        $request->validate([
            'nominal' => 'required',
            'bukti_infaq' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'nullable|in:Menunggu Diterima,Diterima',
            'tanggal_infaq' => 'nullable',
        ]);

        $buktiInfaq = $request->file('bukti_infaq');
        $buktiInfaqName = time().'.'.$buktiInfaq->getClientOriginalExtension();
        $buktiInfaq->storeAs('public/bukti_infaq', $buktiInfaqName);

        $infaq = Infaq::create([
            'nominal' => $request->nominal,
            'bukti_infaq' => $buktiInfaqName,
            'status' => 'Menunggu Diterima',
            'tanggal_infaq' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Infaq berhasil disimpan',
            'data' => $infaq
        ], 201);
    }

    //verifikasi infaq dan status menunggu diterima menjadi diterima yg dilakukan oleh bendhara
    //nominal infaq masuk ke tabel jurnal kas, kategori kas majid dengan jenis kas masuk
    //bukti infaq yang sudah diverifikasi disimpan di storage/app/public/bukti_infaq
    //buatkan
    public function TerimaInfaq(Request $request, $id)
    {
    $infaq = Infaq::find($id);

    if (!$infaq) {
        return response()->json([
            'success' => false,
            'message' => 'Infaq tidak ditemukan',
        ], 404);
    }

    // ❗ cek status biar tidak dobel masuk kas
    if ($infaq->status == 'Diterima') {
        return response()->json([
            'success' => false,
            'message' => 'Infaq sudah diverifikasi sebelumnya',
        ], 400);
    }

    DB::transaction(function () use ($infaq) {

        // 🔥 update status
        $infaq->update([
            'status' => 'Diterima',
        ]);

        // 🔥 masuk ke jurnal kas
        JurnalKas::create([
            'kategori_kas_id' => 2, // misal: Kas Masjid (sesuaikan ID kamu)
            'infaq_id' => $infaq->id,
            'jenis_kas' => 'Masuk',
            'tanggal' => now(),
            'keterangan' => 'Infaq masuk ID #' . $infaq->id,
            'nominal' => $infaq->nominal,
        ]);

    });

    return response()->json([
        'success' => true,
        'message' => 'Infaq berhasil diterima dan masuk ke jurnal kas',
        'data' => $infaq
    ], 200);
    }

}
