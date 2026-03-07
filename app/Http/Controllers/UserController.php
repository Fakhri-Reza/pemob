<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json(['status' => 'success', 'data' => $users]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:donatur,penggalang_dana,admin',
            'no_hp' => 'nullable|string|max:20'
        ]);

        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'no_hp' => $request->no_hp
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User berhasil ditambahkan',
            'data' => $user
        ]);
    }

    public function show($id)
    {
        $user = User::find($id);
        if($user) {
            return response()->json(['status' => 'success', 'data' => $user]);
        }
        return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if($user) {
            $user->update($request->all());
            return response()->json(['status' => 'success', 'message' => 'User berhasil diupdate', 'data' => $user]);
        }
        return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if($user) {
            $user->delete();
            return response()->json(['status' => 'success', 'message' => 'User berhasil dihapus']);
        }
        return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);
    }
}