<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StrukturPengurus;

class StrukturPengurusController
{
    //crud api json struktur pengurus
    public function index()
    {
        return response()->json([
            'message' => 'Struktur Pengurus',
            'data' => StrukturPengurus::all()
        ], 200);
    }

    public function show($id)
    {
        $strukturPengurus = StrukturPengurus::find($id);

        if (!$strukturPengurus) {
            return response()->json([
                'message' => 'Struktur Pengurus tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail Struktur Pengurus',
            'data' => $strukturPengurus
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
        'nama' => 'required|string|max:50',
        'jabatan' => 'required|string|max:50',
        'foto_pengurus' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'periode' => 'required|string|max:20',
        ]);

        $namaFoto = null; // Default jika tidak ada foto

        //foto pengurus disimpan di storage/app/public/foto_pengurus
        if ($request->hasFile('foto_pengurus')) {
        $foto = $request->file('foto_pengurus');
        $namaFoto = time() . '.' . $foto->getClientOriginalExtension();
        $foto->storeAs('foto_pengurus', $namaFoto, 'public');

        }
        $data = StrukturPengurus::create([
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'foto_pengurus' => $namaFoto,
            'periode' => $request->periode,
        ]);

        return response()->json([
        'message' => 'Struktur Pengurus berhasil ditambahkan',
        'data' => $data
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $strukturPengurus = StrukturPengurus::find($id);

        if (!$strukturPengurus) {
            return response()->json([
                'message' => 'Struktur Pengurus tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'nama' => 'required|string|max:50',
            'jabatan' => 'required|string|max:50',
            'foto_pengurus' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'periode' => 'required|string|max:20',
        ]);

        //foto struktur pengurus disimpan di storage/app/public/foto_pengurus
        if ($request->hasFile('foto_pengurus')) {
            $foto = $request->file('foto_pengurus');
            $namaFoto = time().'.'.$foto->getClientOriginalExtension();
            $foto->storeAs('foto_pengurus', $namaFoto, 'public');
            $strukturPengurus->foto_pengurus = $namaFoto;
        }

        $strukturPengurus->nama = $request->nama;
        $strukturPengurus->jabatan = $request->jabatan;
        $strukturPengurus->periode = $request->periode;
        $strukturPengurus->save();

        return response()->json([
            'message' => 'Struktur Pengurus berhasil diupdate',
            'data' => $strukturPengurus
        ], 200);
    }

    public function destroy($id)
    {
        $strukturPengurus = StrukturPengurus::find($id);

        if (!$strukturPengurus) {
            return response()->json([
                'message' => 'Struktur Pengurus tidak ditemukan'
            ], 404);
        }

        $strukturPengurus->delete();

        return response()->json([
            'message' => 'Struktur Pengurus berhasil dihapus'
        ], 200);
    }
}
