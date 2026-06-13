<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataAlmarhum;
use App\Models\Alkah;
use Illuminate\Support\Facades\Validator;
use App\Models\LogAktivitas;

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

        $cek = DataAlmarhum::where(
        'alkah_id',
        $request->alkah_id
        )->exists();

             if ($cek) {
        return response()->json([
        'success' => false,
        'message' => 'Alkah sudah terisi data almarhum'
        ], 400);
     }

        $dataAlmarhum = DataAlmarhum::create($request->all());
        //update status alkah menjadi terisi
        $this->cekStatusAlkah($request->alkah_id);
        $alkah = Alkah::find($request->alkah_id);
        //log aktivitas kode alkah dan nama almarhum
        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Alkah '.$alkah->kode_alkah.' terisi data almarhum '.$request->nama_almarhum,
            'waktu' => now(),
        ]);
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
            $alkah = Alkah::find($request->alkah_id);
            //log aktivitas
            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Data Almarhum '.$dataAlmarhum->nama_almarhum.'di alkah '.$alkah->kode_alkah.' berhasil diubah',
                'waktu' => now(),
            ]);
            $this->cekStatusAlkah($request->alkah_id);
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
            if ($dataAlmarhum){
                $dataAlmarhum->delete();
                    $alkah = Alkah::find($dataAlmarhum->alkah_id);
                    //update status alkah menjadi tersedia
                    $this->cekStatusAlkah($dataAlmarhum->alkah_id);
                    //log aktivitas
                    LogAktivitas::create([
                        'user_id' => auth()->id(),
                        'aktivitas' => 'Data Almarhum '.$dataAlmarhum->nama_almarhum.'di alkah '.$alkah->kode_alkah.' berhasil dihapus',
                        'waktu' => now(),
                    ]);
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
    //jika data almarhum ditambah maka alkah terisi, jika data almarhum dihapus maka alkah tersedia
    private function cekStatusAlkah($alkahId)
     {
         $alkah = Alkah::find($alkahId);
         if ($alkah) {
             if (DataAlmarhum::where('alkah_id', $alkahId)->exists()) {
                 $alkah->status = 'Terisi';
             } else {
                 $alkah->status = 'Tersedia';
             }
             $alkah->save();
         }
     }
}

