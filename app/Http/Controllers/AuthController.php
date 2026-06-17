<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;


 class AuthController
{
    //register
    public function register(Request $request)
{
    $request->validate([
        'nama' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6'
    ]);

    $user = User::create([
        'nama' => $request->nama,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role' => 'Jamaah'
    ]);

    return response()->json([
        'message' => 'Registrasi berhasil',
        'user' => $user,
    ]);
}

    //login
    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Login gagal, email atau password salah'], 401);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Login berhasil',
        'token' => $token,
        'user' => $user
    ]);
    }

   //logout
    public function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logout berhasil']);

 }

 //rest password dengan email otp
    public function resetPassword(Request $request)
    {
    $request->validate([
        'email' => 'required|email|exists:users,email',
    ]);

    $otp = rand(100000, 999999);

    DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->delete();

    DB::table('password_reset_tokens')
        ->insert([
            'email' => $request->email,
            'token' => $otp,
            'created_at' => now(),
        ]);

    Mail::raw(
        'Kode OTP reset password Anda adalah: ' . $otp,
        function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('Reset Password Masjid Alkah');
        }
    );

    return response()->json([
        'success' => true,
        'message' => 'Kode OTP telah dikirim ke email Anda. Silakan cek Inbox dan folder spam.'
    ], 200);
    }

    //update password dengan otp
    public function updatePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|digits:6',
            'password' => 'required|min:8|confirmed',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();
        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid',
            ], 400);
        }
        //update password
        $user = User::where('email', $request->email)->first();
        $user->password = bcrypt($request->password);
        $user->save();

        //hapus token setelah berhasil reset password
        DB::table('password_reset_tokens')->where('email', $request->email)
        ->where('token', $request->token)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah',
        ], 200);
    }
}
