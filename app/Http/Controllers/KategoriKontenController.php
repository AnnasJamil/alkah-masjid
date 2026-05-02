<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriKonten;
use App\Models\KontenWeb;
use Illuminate\Support\Facades\Validator;

class KategoriKontenController
{
    //crud api json untuk kategori konten
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'List Kategori Konten',
            'data' => KategoriKonten::all()
        ], 200);
    }

    public function show($id)
    {
        $kategori = KategoriKonten::find($id);
        if ($kategori) {
            return response()->json([
                'success' => true,
                'message' => 'Detail Kategori Konten',
                'data' => $kategori
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Kategori Konten tidak ditemukan',
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:50|unique:kategori_kontens,nama_kategori',
        ]);
        $kategori = KategoriKonten::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Kategori Konten berhasil ditambahkan',
            'data' => $kategori
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:50|unique:kategori_konten,nama_kategori,'.$id,
        ]);
        $kategori = KategoriKonten::find($id);
        if ($kategori) {
            $kategori->update($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Kategori Konten berhasil diupdate',
                'data' => $kategori
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Kategori Konten tidak ditemukan',
            ], 404);
        }
    }

    public function destroy($id)
    {
        $kategori = KategoriKonten::find($id);
        if ($kategori) {
            //hapus juga konten web yang terkait dengan kategori konten ini
            KontenWeb::where('kategori_konten_id', $id)->delete();
            $kategori->delete();
            return response()->json([
                'success' => true,
                'message' => 'Kategori Konten berhasil dihapus',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Kategori Konten tidak ditemukan',
            ], 404);
        }
    }
}

