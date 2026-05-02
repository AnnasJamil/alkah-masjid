<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DaftarTPA;

class DaftarTPAController
{
    //crud api json daftar tpa
    public function index()
    {
        return response()->json([
            'message' => 'Daftar TPA',
            'data' => DaftarTPA::all()
        ], 200);
    }

    public function show($id)
    {
        $daftarTPA = DaftarTPA::find($id);

        if (!$daftarTPA) {
            return response()->json([
                'message' => 'Daftar TPA tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Detail Daftar TPA',
            'data' => $daftarTPA
        ], 200);
    }

    public function store(Request $request)
    {
        request()->validate([
            'nama_anak' => 'required|string|max:50',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'required|date',
            'nama_orangtua' => 'required|string|max:50',
            'no_hp' => 'required|string|max:15',
            'alamat' => 'required|string',
        ]);

        return response()->json([
            'message' => 'Daftar TPA berhasil ditambahkan',
            'data' => DaftarTPA::create($request->all())
        ], 201);

    }

    public function update(Request $request, $id)
    {
        $daftarTPA = DaftarTPA::find($id);

        if (!$daftarTPA) {
            return response()->json([
                'message' => 'Daftar TPA tidak ditemukan'
            ], 404);
        }

        request()->validate([
            'nama_anak' => 'required|string|max:50',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'required|date',
            'nama_orangtua' => 'required|string|max:50',
            'no_hp' => 'required|string|max:15',
            'alamat' => 'required|string',
        ]);

        $daftarTPA->update($request->all());

        return response()->json([
            'message' => 'Daftar TPA berhasil diupdate',
            'data' => $daftarTPA
        ], 200);
    }

    public function destroy($id)
    {
        $daftarTPA = DaftarTPA::find($id);

        if (!$daftarTPA) {
            return response()->json([
                'message' => 'Daftar TPA tidak ditemukan'
            ], 404);
        }

        $daftarTPA->delete();

        return response()->json([
            'message' => 'Daftar TPA berhasil dihapus',
            'data' => $daftarTPA
        ], 200);
    }
}
