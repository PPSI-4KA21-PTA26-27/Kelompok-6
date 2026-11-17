<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $admin = User::where('email', $data['email'])->first();

        // Pesan sama untuk email salah maupun kata sandi salah.
        if (! $admin || ! Hash::check($data['password'], $admin->password)) {
            throw ValidationException::withMessages(['email' => 'Email atau kata sandi salah.']);
        }

        return response()->json([
            'token' => $admin->createToken('admin')->plainTextToken,
            'admin' => ['id' => $admin->id, 'nama' => $admin->name, 'email' => $admin->email],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Anda telah keluar.']);
    }

    public function me(Request $request)
    {
        $u = $request->user();

        return response()->json(['admin' => ['id' => $u->id, 'nama' => $u->name, 'email' => $u->email]]);
    }
}
