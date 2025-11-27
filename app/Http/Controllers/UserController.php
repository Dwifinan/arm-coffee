<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Pastikan Anda menggunakan Model User yang benar
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule; // Import untuk validasi role
use Illuminate\Support\Facades\Auth; // Tambahkan untuk otorisasi sederhana

class UserController extends Controller
{
    /**
     * Menampilkan daftar users.
     * Dipanggil oleh route('users.index')
     */
    public function index()
    {
        // Mengambil semua data user dari database
        return view('pages.users', [
            "users" => User::all()
        ]);
    }

    /**
     * Menampilkan formulir untuk membuat user baru.
     * Dipanggil oleh route('users.create')
     */
    public function create()
    {
        // Mengarahkan ke view yang berada di resources/views/pages/create.blade.php
        return view('pages.create');
    }

    /**
     * Menyimpan user baru ke database.
     * Dipanggil oleh route('users.store')
     */
    public function store(Request $request)
    {
        // 1. Validasi Data
        $request->validate([
            'username' => 'required|string|max:30|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['owner', 'staff'])],
        ]);

        // 2. Buat User Baru
        try {
            User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password), // WAJIB di-hash
                'role' => $request->role,
            ]);

            // 3. Redirect dengan pesan sukses
            return redirect()->route('users.index')
                ->with('success', 'User ' . $request->username . ' berhasil ditambahkan!');

        } catch (\Exception $e) {
             return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    // =========================================================
    // BARU: EDIT DAN UPDATE
    // =========================================================

    /**
     * Menampilkan formulir edit user.
     * Dipanggil oleh route('users.edit')
     */
    public function edit(User $user)
    {
        // Mengarahkan ke view edit dengan data user
        return view('pages.edit', compact('user'));
    }

    /**
     * Memperbarui user yang ada di database.
     * Dipanggil oleh route('users.update')
     */
    public function update(Request $request, User $user)
    {
        // 1. Validasi Data
        $request->validate([
            // Username harus unik, kecuali untuk user yang sedang di-edit
            'username' => ['required', 'string', 'max:30', Rule::unique('users')->ignore($user->id)],
            // Password bersifat opsional saat edit. Jika diisi, harus minimal 8 karakter dan dikonfirmasi.
            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', Rule::in(['owner', 'staff'])],
        ]);

        // 2. Siapkan data update
        $data = [
            'username' => $request->username,
            'role' => $request->role,
        ];

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // 3. Update User
        $user->update($data);

        // 4. Redirect dengan pesan sukses
        return redirect()->route('users.index')
            ->with('success', 'User ' . $user->username . ' berhasil diperbarui!');
    }

    // =========================================================
    // BARU: DESTROY (HAPUS)
    // =========================================================

    /**
     * Menghapus user dari database.
     * Dipanggil oleh route('users.destroy')
     */
    public function destroy(User $user)
    {
        // Cek otorisasi sederhana: User tidak boleh menghapus dirinya sendiri
        if (Auth::user()->id === $user->id) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Hapus User
        try {
            $user->delete();
            return redirect()->route('users.index')
                ->with('success', 'User ' . $user->username . ' berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }
}
