<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JurnalKas;
use App\Models\LogAktivitas;

class JurnalKasController
{
    //crud jurnal kas api json tanpa kategori kas
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Menampilkan semua jurnal kas',
            'data' => JurnalKas::all()
        ], 200);
    }

    public function show($id)
    {
        $jurnalKas = JurnalKas::find($id);
        if ($jurnalKas) {
            return response()->json([
                'success' => true,
                'message' => 'Menampilkan jurnal kas',
                'data' => $jurnalKas
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Jurnal kas tidak ditemukan',
            ], 404);
        }
    }

    //isi jurnal kas berupa pemasukan atau pengeluaran, jika pemasukan maka nominal positif, jika pengeluaran maka nominal negatif
    public function store(Request $request)
    {
        $request->validate([
            'jenis_kas' => 'required|in:Masuk,Keluar',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
            'nominal' => 'required|numeric',
        ]);

        $jurnalKas = JurnalKas::create($request->all());

        //log aktivitas
        LogAktivitas::create([
            'user_id' => $request->user()->id, // pastikan user sudah login
            'aktivitas' => 'Menambahkan jurnal kas ' . $jurnalKas->jenis_kas . ' dengan nominal ' . $jurnalKas->nominal,
            'waktu' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jurnal kas berhasil ditambahkan',
            'data' => $jurnalKas
        ], 201);
    }
}
