<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KontenWeb;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\user;

class KontenWebController
{
    // ==========================
    // LIST KONTEN
    // ==========================
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'List Konten Web',
            'data' => KontenWeb::with('user')
                ->latest()
                ->get()
        ], 200);
    }

    // ==========================
    // DETAIL KONTEN
    // ==========================
    public function show($id)
    {
        $konten = KontenWeb::with('user')->find($id);

        if (!$konten) {
            return response()->json([
                'success' => false,
                'message' => 'Konten Web tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail Konten Web',
            'data' => $konten
        ], 200);
    }

    // ==========================
    // TAMBAH KONTEN
    // ==========================
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'kategori' => 'required|in:Berita,Pengumuman,Kegiatan',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        $gambar = null;

        if ($request->hasFile('gambar')) {
            $gambar = $request->file('gambar')
                ->store('konten_web', 'public');
        }

        $konten = KontenWeb::create([
            'user_id' => auth()->id(),
            'judul' => $request->judul,
            'isi' => $request->isi,
            'kategori' => $request->kategori,
            'tanggal_publish' =>  $request->status == 'published' ? now() : ($request->tanggal_publish ?? now()),
            'gambar' => $gambar,
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Konten Web berhasil dibuat',
            'data' => $konten
        ], 201);
    }

    // ==========================
    // UPDATE KONTEN
    // ==========================
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
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'kategori' => 'required|in:Berita,Pengumuman,Kegiatan',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status' => 'required|in:draft,published',
        ]);

        if ($request->hasFile('gambar')) {

            if ($konten->gambar) {
                Storage::disk('public')
                    ->delete($konten->gambar);
            }

            $gambar = $request->file('gambar')
                ->store('konten_web', 'public');

            $konten->gambar = $gambar;
        }

        $konten->user_id = auth()->id();
        $konten->judul = $request->judul;
        $konten->isi = $request->isi;
        $konten->kategori = $request->kategori;
        $konten->status = $request->status;

        if ($request->status == 'published') {
            $konten->tanggal_publish = now();
        } else {
            $konten->tanggal_publish =
                $request->tanggal_publish
                ?? $konten->tanggal_publish;
        }

        $konten->save();

        return response()->json([
            'success' => true,
            'message' => 'Konten berhasil diupdate',
            'data' => $konten
        ], 200);
    }

    // ==========================
    // HAPUS KONTEN
    // ==========================
    public function destroy($id)
    {
        $konten = KontenWeb::find($id);

        if (!$konten) {
            return response()->json([
                'success' => false,
                'message' => 'Konten Web tidak ditemukan'
            ], 404);
        }

        if ($konten->gambar) {
            Storage::disk('public')
                ->delete($konten->gambar);
        }

        $konten->delete();

        return response()->json([
            'success' => true,
            'message' => 'Konten Web berhasil dihapus'
        ], 200);
    }

    // ==========================
    // KONTEN PUBLISHED
    // ==========================
    public function published()
    {
        $data = KontenWeb::with('user')
            ->where('status', 'published')
            ->orderBy('tanggal_publish', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    // ==========================
    // FILTER KATEGORI
    // ==========================
    public function kategori($kategori)
    {
        $data = KontenWeb::with('user')
            ->where('kategori', $kategori)
            ->where('status', 'published')
            ->orderBy('tanggal_publish', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
