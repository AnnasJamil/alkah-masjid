<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TargetInfaq;
use App\Models\Infaq;
use App\Models\JurnalKas;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class TargetInfaqController
{
    //crud api json untuk target infaq
   public function index()
    {
    $targets = TargetInfaq::all()->map(function ($target) {

        $danaTerkumpul = Infaq::where(
            'target_infaq_id',
            $target->id
        )
        ->where('status', 'Diterima')
        ->sum('nominal');

        $sisaDana =
            $target->target_dana -
            $danaTerkumpul;

        $persentase = 0;

        if ($target->target_dana > 0) {
            $persentase = round(
                ($danaTerkumpul / $target->target_dana) * 100,
                2
            );
        }

        $status =
        $danaTerkumpul >= $target->target_dana
        ? 'Selesai'
        : 'Aktif';

        return [
            'id' => $target->id,
            'nama_target' => $target->nama_target,
            'target_dana' => $target->target_dana,
            'dana_terkumpul' => $danaTerkumpul,
            'sisa_dana' => $sisaDana,
            'persentase' => $persentase . '%',
            'status' => $status,
            'donatur' => $target->infaqs->map(function ($item) {
                return [
                    'nama' => $item->nama_penginfaq,
                    'nominal' => $item->nominal,
                    'tanggal' => $item->tanggal_infaq,
                ];
            })
        ];
    });

    return response()->json([
        'success' => true,
        'message' => 'List Target Infaq',
        'data' => $targets
    ], 200);
    }

    public function show($id)
    {
    $targetInfaq = TargetInfaq::find($id);

    if (!$targetInfaq) {
        return response()->json([
            'success' => false,
            'message' => 'Target Infaq tidak ditemukan'
        ], 404);
    }

    $danaTerkumpul = Infaq::where('target_infaq_id', $id)
        ->where('status', 'Diterima')
        ->sum('nominal');

    $sisaDana = $targetInfaq->target_dana - $danaTerkumpul;

    $persentase = 0;

    if ($targetInfaq->target_dana > 0) {
        $persentase = round(
            ($danaTerkumpul / $targetInfaq->target_dana) * 100,
            2
        );
    }

    $status =
        $danaTerkumpul >= $targetInfaq->target_dana
            ? 'Selesai'
            : 'Aktif';

    return response()->json([
        'success' => true,
        'message' => 'Detail Target Infaq',
        'data' => [
            'id' => $targetInfaq->id,
            'nama_target' => $targetInfaq->nama_target,
            'target_dana' => $targetInfaq->target_dana,
            'dana_terkumpul' => $danaTerkumpul,
            'sisa_dana' => $sisaDana,
            'persentase' => $persentase . '%',
            'status' => $status,
            'donatur' => $targetInfaq->infaqs->map(function ($item) {
                return [
                    'nama' => $item->nama_penginfaq,
                    'nominal' => $item->nominal,
                    'tanggal' => $item->tanggal_infaq,
                ];
            })
        ]
    ], 200);
    }

   public function store(Request $request)
    {
    $request->validate([
        'nama_target' => 'required|string|max:255',
        'target_dana' => 'required|numeric|min:1000',
    ]);

    $targetInfaq = TargetInfaq::create([
        'nama_target' => $request->nama_target,
        'target_dana' => $request->target_dana,
        'status' => 'Aktif',
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Target Infaq berhasil dibuat',
        'data' => $targetInfaq
    ], 201);
    }

    public function update(Request $request, $id)
    {
    $targetInfaq = TargetInfaq::find($id);

    if (!$targetInfaq) {
        return response()->json([
            'success' => false,
            'message' => 'Target Infaq tidak ditemukan'
        ], 404);
    }

    $request->validate([
        'nama_target' => 'required|string|max:255',
        'target_dana' => 'required|numeric|min:1000',
    ]);

    $targetInfaq->update([
        'nama_target' => $request->nama_target,
        'target_dana' => $request->target_dana,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Target Infaq berhasil diperbarui',
        'data' => $targetInfaq
    ], 200);
    }

    public function destroy($id)
    {
    $targetInfaq = TargetInfaq::find($id);

    if (!$targetInfaq) {
        return response()->json([
            'success' => false,
            'message' => 'Target Infaq tidak ditemukan'
        ], 404);
    }

    $adaDonasi = Infaq::where(
        'target_infaq_id',
        $id
    )->exists();

    if ($adaDonasi) {
        return response()->json([
            'success' => false,
            'message' => 'Target sudah memiliki data donasi dan tidak dapat dihapus'
        ], 400);
    }

    $targetInfaq->delete();

    return response()->json([
        'success' => true,
        'message' => 'Target Infaq berhasil dihapus'
    ], 200);
    }
}
