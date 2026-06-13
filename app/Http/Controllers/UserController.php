<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\LogAktivitas;

class UserController
{
    // LIST USER
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'List User',
            'data' => User::whereIn('role', ['Bendahara', 'Pengelola Alkah'])->get()
        ], 200);
    }

    // DETAIL USER
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail User',
            'data' => $user
        ], 200);
    }

    // TAMBAH USER
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required|in:Bendahara,Pengelola Alkah'
        ]);

        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        //log aktivitas
        LogAktivitas::create([
            'user_id' => auth()->user()->id,
            'aktivitas' => 'Menambahkan role ' . $request->role . ' dengan nama ' . $request->nama,
            'waktu' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dibuat',
            'data' => $user
        ], 201);
    }

    // UPDATE USER
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:Bendahara,Pengelola Alkah'
        ]);

        //hanya user bendahara dan pengelola alkah yang bisa diupdate
        if (!in_array($user->role, ['Bendahara', 'Pengelola Alkah'])) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Bendahara dan Pengelola Alkah yang bisa diupdate'
            ], 400);
        }

        $user->update([
            'nama' => $request->nama,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        //log aktivitas
        LogAktivitas::create([
            'user_id' => auth()->user()->id,
            'aktivitas' => 'Mengubah role ' . $request->role . ' dengan nama ' . $request->nama,
            'waktu' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil diupdate',
            'data' => $user
        ], 200);
    }

    // RESET PASSWORD USER
    public function resetPassword(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'password' => 'required|min:8'
        ]);

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        //log aktivitas
        LogAktivitas::create([
            'user_id' => auth()->user()->id,
            'aktivitas' => 'Meriset password role ' . $user->role . ' dengan nama ' . $user->nama,
            'waktu' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil direset'
        ]);
    }

    // HAPUS USER
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        if ($user->role == 'Admin' || $user->role == 'Jamaah') {
            return response()->json([
                'success' => false,
                'message' => 'Admin dan Jamaah tidak boleh dihapus'
            ], 400);
        }

        $user->delete();

        //log aktivitas
        LogAktivitas::create([
            'user_id' => auth()->user()->id,
            'aktivitas' => 'Menghapus role ' . $user->role . ' dengan nama ' . $user->nama,
            'waktu' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus'
        ], 200);
    }
}
