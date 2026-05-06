<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LogAktivitas;

class LogAktivitasController
{
     public function index()
    {
        $data = LogAktivitas::with('user')
            ->orderBy('waktu', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data log aktivitas',
            'data' => $data
        ], 200);
    }
}
