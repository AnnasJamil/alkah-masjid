<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlokAlkah;
use Illuminate\Support\Facades\Validator;

class BlokAlkahController
{
    //crud blok alkah api json
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'List Blok Alkah',
            'data' => BlokAlkah::all()
        ], 200);
    }

    //hanya kode blok dan status blok yang ditampilkan untuk pemesanan alkah
    public function show($id)
    {
        $blok = BlokAlkah::find($id);
        if ($blok) {
            return response()->json([
                'success' => true,
                'message' => 'Detail Blok Alkah',
                'data' => $blok
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Blok Alkah tidak ditemukan',
            ], 404);
        }
    }

    //status Tersedia dan Penuh. defaultnya status Tersedia
    //kenapa statusnya tidak dibuat default Tersedia? karena saat pembuatan blok alkah, statusnya bisa langsung diisi Penuh jika memang sudah ada pemesanannya
    // jadi tidak harus default Tersedia?
    public function store(Request $request)
    {
        $request->validate([
            'kode_blok' => 'required|string|max:3|unique:blok_alkahs,kode_blok',
            'status' => 'nullable|in:Tersedia,Penuh',
        ]);
        $blok = BlokAlkah::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Blok Alkah berhasil ditambahkan',
            'data' => $blok
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_blok' => 'required|string|max:3|unique:blok_alkahs,kode_blok,'.$id,
            'status' => 'nullable|in:Tersedia,Penuh',
        ]);
        $blok = BlokAlkah::find($id);
        if ($blok) {
            $blok->update($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Blok Alkah berhasil diupdate',
                'data' => $blok
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Blok Alkah tidak ditemukan',
            ], 404);
        }
    }

    public function destroy($id)
    {
        $blok = BlokAlkah::find($id);
        if ($blok) {
            $blok->delete();
            return response()->json([
                'success' => true,
                'message' => 'Blok Alkah berhasil dihapus',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Blok Alkah tidak ditemukan',
            ], 404);
        }
    }
}
