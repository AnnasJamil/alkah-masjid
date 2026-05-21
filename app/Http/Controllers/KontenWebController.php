<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KontenWeb;
use Illuminate\Support\Facades\Storage;
use App\Models\KategoriKonten;
use App\Models\User;
use App\Models\LogAktivitas;

class KontenWebController
{
    //crud api json untuk konten web
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'List Konten Web',
            'data' => KontenWeb::with('kategoriKonten', 'user')->get()
        ], 200);
    }

    public function show($id)
    {
        $konten = KontenWeb::with('kategoriKonten', 'user')->find($id);
        if ($konten) {
            return response()->json([
                'success' => true,
                'message' => 'Detail Konten Web',
                'data' => $konten
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Konten Web tidak ditemukan',
            ], 404);
        }
    }

    //gambar di upload ke folder storage/app/public/konten_web
    //tanggal publish diisi otomatis dengan tanggal sekarang jika status published, jika draft bisa diisi manual atau otomatis dengan tanggal sekarang

        public function store(Request $request)
    {
        $request->validate([
            'kategori_konten_id' => 'required|exists:kategori_kontens,id',
            'user_id' => 'required|exists:users,id',
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'tanggal_publish' => 'nullable|datetime',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        $konten = new KontenWeb();
        $konten->kategori_konten_id = $request->kategori_konten_id;
        $konten->user_id = $request->user_id;
        $konten->judul = $request->judul;
        $konten->isi = $request->isi;

        if ($request->status == 'published') {
            $konten->tanggal_publish = now();
        } else {
            $konten->tanggal_publish = $request->tanggal_publish ?? now();
        }

        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar')->store('konten_web', 'public');
            $konten->gambar = $gambar;
        }

        $konten->status = $request->status;
        $konten->save();

        return response()->json([
            'success' => true,
            'message' => 'Konten Web berhasil dibuat',
            'data' => $konten
        ], 201);
    }

    public function update(Request $request, $id)
    {
    $konten = KontenWeb::find($id);

    if (!$konten) {
        return response()->json([
            'success' => false,
            'message' => 'Konten tidak ditemukan'
        ], 404);
    }

    $request->validate([
        'kategori_konten_id' => 'required|exists:kategori_kontens,id',
        'user_id' => 'required|exists:users,id',
        'judul' => 'required|string|max:255',
        'isi' => 'required|string',
        'tanggal_publish' => 'nullable|datetime',
        'gambar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        'status' => 'required|in:draft,published',
    ]);

    $konten->kategori_konten_id = $request->kategori_konten_id;
    $konten->user_id = $request->user_id;
    $konten->judul = $request->judul;
    $konten->isi = $request->isi;

    if ($request->status == 'published') {
        $konten->tanggal_publish = now();
    } else {
        $konten->tanggal_publish = $request->tanggal_publish ?? $konten->tanggal_publish;
    }

    // update gambar (hapus lama)
    if ($request->hasFile('gambar')) {
        // hapus gambar lama jika ada
        if ($konten->gambar) {
            Storage::disk('public')->delete($konten->gambar);
        }
        $gambar = $request->file('gambar')->store('konten_web', 'public');
        $konten->gambar = $gambar;
    }

    $konten->status = $request->status;
    $konten->save();

    return response()->json([
        'success' => true,
        'message' => 'Konten berhasil diupdate',
        'data' => $konten
    ], 200);
    }

    public function destroy($id)
    {
        $konten = KontenWeb::find($id);
        if ($konten) {
            $konten->delete();
            return response()->json([
                'success' => true,
                'message' => 'Konten Web berhasil dihapus',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Konten Web tidak ditemukan',
            ], 404);
        }
    }

    public function published()
    {
    $data = KontenWeb::with('kategoriKonten')
        ->where('status', 'published')
        ->orderBy('tanggal_publish', 'desc')
        ->get();

    return response()->json([
        'success' => true,
        'data' => $data
    ]);
    }


}
