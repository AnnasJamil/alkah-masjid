<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JurnalKas;
use App\Models\LogAktivitas;
use Carbon\Carbon;

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

    //update jurnal kas
    public function update(Request $request, $id)
    {
        $jurnalKas = JurnalKas::find($id);
        if ($jurnalKas) {
            $request->validate([
                'jenis_kas' => 'required|in:Masuk,Keluar',
                'tanggal' => 'required|date',
                'keterangan' => 'nullable|string',
                'nominal' => 'required|numeric',
            ]);

            $jurnalKas->update($request->all());

            //log aktivitas
            LogAktivitas::create([
                'user_id' => $request->user()->id, // pastikan user sudah login
                'aktivitas' => 'Mengubah jurnal kas ' . $jurnalKas->jenis_kas . ' dengan nominal ' . $jurnalKas->nominal,
                'waktu' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jurnal kas berhasil diubah',
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

    //delete jurnal kas
    public function destroy($id)
    {
        $jurnalKas = JurnalKas::find($id);
        if ($jurnalKas) {
            $jurnalKas->delete();
            //log aktivitas
            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Menghapus jurnal kas ' . $jurnalKas->jenis_kas . ' tentang ' . $jurnalKas->keterangan . ' dengan nominal ' . $jurnalKas->nominal,
                'waktu' => now(),
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Jurnal kas berhasil dihapus',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Jurnal kas tidak ditemukan',
            ], 404);
        }
    }

    //laporan kas mingguan dihari jumat sampai jumat
    public function LaporanMingguan()
    {
    // TANGGAL SEKARANG
    $hariIni = Carbon::now();

    // CARI JUMAT MINGGU INI
    $jumatIni = Carbon::now()->next(Carbon::FRIDAY);

    // kalau hari ini jumat
    if ($hariIni->isFriday()) {
        $jumatIni = $hariIni;
    }
    // JUMAT LALU
    $jumatLalu = Carbon::parse($jumatIni)->subWeek();

    // JUMAT 2 MINGGU LALU
    $jumatDuaMingguLalu = Carbon::parse($jumatLalu)->subWeek();

    // SALDO JUMAT LALU
    // hanya hitung 1 minggu sebelumnya
    $totalMasukSebelumnya = JurnalKas::where('jenis_kas', 'Masuk')
        ->whereBetween('tanggal', [$jumatDuaMingguLalu, $jumatLalu])
        ->sum('nominal');

    $totalKeluarSebelumnya = JurnalKas::where('jenis_kas', 'Keluar')
        ->whereBetween('tanggal', [$jumatDuaMingguLalu, $jumatLalu])
        ->sum('nominal');

    $saldoJumatLalu =
        $totalMasukSebelumnya - $totalKeluarSebelumnya;

    // PEMASUKAN MINGGU INI
    $pemasukan = JurnalKas::where('jenis_kas', 'Masuk')
        ->whereBetween('tanggal', [$jumatLalu, $jumatIni])
        ->sum('nominal');

    // PENGELUARAN MINGGU INI
    $pengeluaran = JurnalKas::where('jenis_kas', 'Keluar')
        ->whereBetween('tanggal', [$jumatLalu, $jumatIni])
        ->sum('nominal');

    // SALDO SEKARANG
    $saldoSekarang = + $pemasukan - $pengeluaran;

    // DETAIL PEMASUKAN
    $detailPemasukan = JurnalKas::where('jenis_kas', 'Masuk')
        ->whereBetween('tanggal', [$jumatLalu, $jumatIni])
        ->get();

    // DETAIL PENGELUARAN
    $detailPengeluaran = JurnalKas::where('jenis_kas', 'Keluar')
        ->whereBetween('tanggal', [$jumatLalu, $jumatIni])
        ->get();

    // RESPONSE JSON
    return response()->json([
        'success' => true,

        'periode' => [
            'saldo_jumat_lalu' => [
                'dari' => $jumatDuaMingguLalu->format('d-m-Y'),
                'sampai' => $jumatLalu->format('d-m-Y'),
            ],

            'minggu_ini' => [
                'dari' => $jumatLalu->format('d-m-Y'),
                'sampai' => $jumatIni->format('d-m-Y'),
            ]
        ],

        'laporan' => [
            'saldo_jumat_lalu' => $saldoJumatLalu,
            'penerimaan_sepekan' => $pemasukan,
            'pengeluaran_sepekan' => $pengeluaran,
            'saldo_saat_ini' => $saldoSekarang,
            'detail_pemasukan' => $detailPemasukan,
            'detail_pengeluaran' => $detailPengeluaran,
        ]
    ]);
    }
}
