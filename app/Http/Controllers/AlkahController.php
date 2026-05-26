<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alkah;
use Illuminate\Support\Facades\Validator;
use App\Models\BlokAlkah;
use App\Models\DataAlmarhum;


class AlkahController
{
    //crud alkah api json
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'List Alkah',
            'data' => Alkah::all()
        ], 200);
    }

    public function show($id)
    {
        $alkah = Alkah::find($id);
        if ($alkah) {
            return response()->json([
                'success' => true,
                'message' => 'Detail Alkah',
                'data' => $alkah
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Alkah tidak ditemukan',
            ], 404);
        }
    }

    //crud tambah alkah api json
    public function store(Request $request)
    {
        $request->validate([
            'blok_alkah_id' => 'required|exists:blok_alkahs,id',
            'kode_alkah' => 'required|string|max:3|unique:alkahs,kode_alkah',
            'harga' => 'required|numeric',
            'status' => 'nullable|in:Tersedia,Terisi,Dipesan',
        ]);
        $alkah = Alkah::create($request->all());
            // cek status blok setelah tambah alkah
            $this->cekStatusBlok($request->blok_alkah_id);
        return response()->json([
            'success' => true,
            'message' => 'Alkah berhasil ditambahkan',
            'data' => $alkah
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $alkah = Alkah::find($id);
        if ($alkah) {
            $request->validate([
                'blok_alkah_id' => 'required|exists:blok_alkahs,id',
                'kode_alkah' => 'required|string|max:3|unique:alkahs,kode_alkah,'.$id,
                'harga' => 'required|numeric',
                'status' => 'nullable|in:Tersedia,Terisi,Dipesan',
            ]);
            $alkah->update($request->all());
            // cek status blok setelah update alkah
            $this->cekStatusBlok($request->blok_alkah_id);
            return response()->json([
                'success' => true,
                'message' => 'Alkah berhasil diupdate',
                'data' => $alkah
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Alkah tidak ditemukan',
            ], 404);
        }
    }

    public function destroy($id)
    {
        $alkah = Alkah::find($id);
        if ($alkah) {
            $alkah->delete();
            return response()->json([
                'success' => true,
                'message' => 'Alkah berhasil dihapus',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Alkah tidak ditemukan',
            ], 404);
        }
    }

    // cek status blok otomatis
    private function cekStatusBlok($blokId)
    {
    // cek apakah masih ada alkah tersedia
    $masihAdaTersedia = Alkah::where(
        'blok_alkah_id',
        $blokId
    )
    ->where('status', 'Tersedia')
    ->exists();

    // jika masih ada yang tersedia
    if ($masihAdaTersedia) {

        BlokAlkah::where('id', $blokId)
            ->update([
                'status' => 'Tersedia'
            ]);

    } else {

        // semua alkah penuh
        BlokAlkah::where('id', $blokId)
            ->update([
                'status' => 'Penuh'
            ]);
    }
    }

}
