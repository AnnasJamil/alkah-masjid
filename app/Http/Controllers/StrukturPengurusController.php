<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StrukturPengurus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\LogAktivitas;
use App\Models\User;

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
        $foto_pengurus = $request->file('foto_pengurus')->store('foto_pengurus', 'public');

        }
        $data = StrukturPengurus::create([
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'foto_pengurus' => $foto_pengurus ?? null, // Simpan nama file jika ada, atau null jika tidak ada
            'periode' => $request->periode,
        ]);

        // Log aktivitas
        $user = auth()->user();
        LogAktivitas::create([
            'user_id' => $user->id,
            'aktivitas' => "Menambahkan struktur pengurus {$data->nama} ({$data->jabatan})",
            'waktu' => now(),
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
            $foto = $request->file('foto_pengurus')->store('foto_pengurus', 'public');
            $strukturPengurus->foto_pengurus = $foto;
        }

        $strukturPengurus->nama = $request->nama;
        $strukturPengurus->jabatan = $request->jabatan;
        $strukturPengurus->periode = $request->periode;
        $strukturPengurus->save();

        // Log aktivitas
        $user = auth()->user();
        LogAktivitas::create([
            'user_id' => $user->id,
            'aktivitas' => "Mengubah struktur pengurus: {$strukturPengurus->nama} ({$strukturPengurus->jabatan})",
            'waktu' => now(),
        ]);

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
        // Log aktivitas
        $user = auth()->user();
        LogAktivitas::create([
            'user_id' => $user->id,
            'aktivitas' => "Menghapus struktur pengurus {$strukturPengurus->nama} ({$strukturPengurus->jabatan})",
            'waktu' => now(),
        ]);
        return response()->json([
            'message' => 'Struktur Pengurus berhasil dihapus'
        ], 200);
    }
}
