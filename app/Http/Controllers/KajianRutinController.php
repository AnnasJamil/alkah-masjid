<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KajianRutin;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class KajianRutinController
{
    //curd api json kajian rutin

    //index
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'List Kajian Rutin',
            'data' => KajianRutin::all()
        ], 200);
    }

    //show
    public function show($id)
    {
        $kajianRutin = KajianRutin::find($id);
        if ($kajianRutin) {
            return response()->json([
                'success' => true,
                'message' => 'Detail Kajian Rutin',
                'data' => $kajianRutin
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Kajian Rutin tidak ditemukan',
            ], 404);
        }
    }

    //store
    public function store(Request $request)
    {
    $request->validate([
        'pemateri' => 'required|string|max:255',
        'judul' => 'required|string|max:255',
        'jadwal' => 'required|string|max:255',
        'jam' => 'required|string|max:255',
        'lokasi' => 'required|string|max:255',
        'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $gambar = null;

    if ($request->hasFile('gambar')) {
        $gambar = $request->file('gambar')
            ->store('gambar_kajian', 'public');
    }

    $kajianRutin = KajianRutin::create([
        'pemateri' => $request->pemateri,
        'judul' => $request->judul,
        'jadwal' => $request->jadwal,
        'jam' => $request->jam,
        'lokasi' => $request->lokasi,
        'gambar' => $gambar,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Kajian Rutin berhasil ditambahkan',
        'data' => $kajianRutin
    ], 201);
    }

    //update
    public function update(Request $request, $id)
    {
    $kajianRutin = KajianRutin::find($id);

    if (!$kajianRutin) {
        return response()->json([
            'success' => false,
            'message' => 'Kajian Rutin tidak ditemukan',
        ], 404);
    }

    $request->validate([
        'pemateri' => 'required|string|max:255',
        'judul' => 'required|string|max:255',
        'jadwal' => 'required|string|max:255',
        'jam' => 'required|string|max:255',
        'lokasi' => 'required|string|max:255',
        'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    $data = [
        'pemateri' => $request->pemateri,
        'judul' => $request->judul,
        'jadwal' => $request->jadwal,
        'jam' => $request->jam,
        'lokasi' => $request->lokasi,
    ];

    if ($request->hasFile('gambar')) {

        if ($kajianRutin->gambar) {
            Storage::disk('public')
                ->delete($kajianRutin->gambar);
        }

        $data['gambar'] = $request->file('gambar')
            ->store('gambar_kajian', 'public');
    }

    $kajianRutin->update($data);

    return response()->json([
        'success' => true,
        'message' => 'Kajian Rutin berhasil diupdate',
        'data' => $kajianRutin->fresh()
    ], 200);
    }

    //delete
    public function destroy($id)
    {
        $kajianRutin = KajianRutin::find($id);
        if ($kajianRutin) {
            Storage::delete('public/' . $kajianRutin->gambar);
            $kajianRutin->delete();
            return response()->json([
                'success' => true,
                'message' => 'Kajian Rutin berhasil dihapus',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Kajian Rutin tidak ditemukan',
            ], 404);
        }
    }
}
