<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriKas;

class KategoriKasController
{
    //api crud kategori kas json
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Menampilkan semua kategori kas',
            'data' => KategoriKas::all()
        ], 200);
    }

    public function show($id)
    {
        $kategoriKas = KategoriKas::find($id);
        if ($kategoriKas) {
            return response()->json([
                'success' => true,
                'message' => 'Menampilkan kategori kas',
                'data' => $kategoriKas
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Kategori kas tidak ditemukan',
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_kas,nama_kategori',
        ]);
        $kategoriKas = KategoriKas::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Kategori kas berhasil ditambahkan',
            'data' => $kategoriKas
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $kategoriKas = KategoriKas::find($id);
        if ($kategoriKas) {
            $request->validate([
                'nama_kategori' => 'required|string|max:255|unique:kategori_kas,nama_kategori,' . $id,
            ]);
            $kategoriKas->update($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Kategori kas berhasil diupdate',
                'data' => $kategoriKas
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Kategori kas tidak ditemukan',
            ], 404);
        }
    }

    public function destroy($id)
    {
        $kategoriKas = KategoriKas::find($id);
        if ($kategoriKas) {
            $kategoriKas->delete();
            return response()->json([
                'success' => true,
                'message' => 'Kategori kas berhasil dihapus',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Kategori kas tidak ditemukan',
            ], 404);
        }
    }
}
