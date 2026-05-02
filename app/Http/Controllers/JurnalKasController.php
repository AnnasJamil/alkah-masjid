<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriKas;
use App\Models\JurnalKas;

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

    
}
