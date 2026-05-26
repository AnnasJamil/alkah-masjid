<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataAlmarhum;
use App\Models\Alkah;

class DataAlmarhumController
{
    //api json untuk data almarhum
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Menampilkan semua data almarhum',
            'data' => DataAlmarhum::all()
        ], 200);
    }

    public function show($id)
    {
        $dataAlmarhum = DataAlmarhum::find($id);
        if ($dataAlmarhum) {
            return response()->json([
                'success' => true,
                'message' => 'Menampilkan data almarhum',
                'data' => $dataAlmarhum
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data almarhum tidak ditemukan',
            ], 404);
        }

    }

    //store data almarhum
    public function store(Request $request)
    {
        $request->validate([
            'alkah_id' => 'required|exists:alkahs,id',
            'nama_almarhum' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'tanggal_wafat' => 'required|date',
            'umur' => 'required|string',
        ]);

        $dataAlmarhum = DataAlmarhum::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Data almarhum berhasil disimpan',
            'data' => $dataAlmarhum
        ], 201);
    }

    //update data almarhum
    public function update(Request $request, $id)
    {
        $dataAlmarhum = DataAlmarhum::find($id);
        if ($dataAlmarhum) {
            $request->validate([
                'alkah_id' => 'required|exists:alkahs,id',
                'nama_almarhum' => 'required|string',
                'tanggal_lahir' => 'required|date',
                'tanggal_wafat' => 'required|date',
                'umur' => 'required|string',
            ]);

            $dataAlmarhum->update($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Data almarhum berhasil diubah',
                'data' => $dataAlmarhum
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data almarhum tidak ditemukan',
            ], 404);
        }
    }
        //destroy data almarhum
        public function destroy($id)
        {
            $dataAlmarhum = DataAlmarhum::find($id);
            if ($dataAlmarhum) {
                $dataAlmarhum->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Data almarhum berhasil dihapus',
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Data almarhum tidak ditemukan',
                ], 404);
            }
        }
}

