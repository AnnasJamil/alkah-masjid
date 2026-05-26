<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProfilJamaah;
use Illuminate\Support\Facades\Storage;

class ProfilJamaahController
{
    // =====================================
    // LIHAT PROFIL SENDIRI
    // =====================================

    public function show(Request $request)
    {
        $profil = ProfilJamaah::where(
            'user_id',
            $request->user()->id
        )->first();

        if (!$profil) {

            return response()->json([
                'success' => false,
                'message' => 'Profil belum diisi'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $profil
        ]);
    }

    // =====================================
    // SIMPAN / UPDATE PROFIL
    // =====================================

    public function store(Request $request)
    {
        $request->validate([

            'nik' => 'required',

            'nama_lengkap' => 'required',

            'tempat_lahir' => 'required',

            'tanggal_lahir' => 'required|date',

            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',

            'alamat' => 'required',

            'no_hp' => 'required',

            'foto_ktp' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'foto_kk' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // =====================================
        // upload foto ktp
        // =====================================

        $fotoKtp = $request->file('foto_ktp')
            ->store('foto_ktp', 'public');

        $fotoKk = $request->file('foto_kk')
            ->store('foto_kk', 'public');

        // =====================================
        // simpan atau update profil
        // =====================================

        $profil = ProfilJamaah::updateOrCreate(

            [
                'user_id' => $request->user()->id
            ],

            [
                'nik' => $request->nik,

                'nama_lengkap' => $request->nama_lengkap,

                'tempat_lahir' => $request->tempat_lahir,

                'tanggal_lahir' => $request->tanggal_lahir,

                'jenis_kelamin' => $request->jenis_kelamin,

                'alamat' => $request->alamat,

                'pekerjaan' => $request->pekerjaan,

                'no_hp' => $request->no_hp,

                'foto_ktp' => $fotoKtp,
                'foto_kk' => $fotoKk,
            ]
        );

        return response()->json([

            'success' => true,

            'message' => 'Profil berhasil disimpan',

            'data' => $profil
        ]);
    }
}
