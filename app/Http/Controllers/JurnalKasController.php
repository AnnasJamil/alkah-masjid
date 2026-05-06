<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriKas;
use App\Models\JurnalKas;
use App\Models\LogAktivitas;

class JurnalKasController
{
    //crud jurnal kas api json
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Menampilkan semua jurnal kas',
            'data' => JurnalKas::with('kategoriKas')->get()
        ], 200);
    }

    public function show($id)
    {
        $jurnalKas = JurnalKas::with('kategoriKas')->find($id);
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
            'kategori_kas_id' => 'required|exists:kategori_kas,id',
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

    //laporan jurnal kas berdasarkan mingguan dari jumat ke jumat, bulanan, dan tahunan
    //jika mingguan maka tampilkan jurnal kas dari jumat ke jumat, jika bulanan maka tampilkan jurnal kas dari tanggal 1 sampai tanggal terakhir di bulan tersebut, jika tahunan maka tampilkan jurnal kas dari tanggal 1 januari sampai tanggal 31 desember di tahun tersebut

}
