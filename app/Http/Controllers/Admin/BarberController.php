<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class BarberController extends Controller
{
    /**
     * Tampilkan daftar semua barber.
     */
    public function index()
    {
        // Ambil semua data barber beserta data user terkait, urutkan terbaru, dan paginasi 10 per halaman.
        $barbers = Barber::with('user')->latest()->paginate(10);

        // Kirim ke view 'admin.barbers.index'
        return view('admin.barbers.index', compact('barbers'));
    }

    /**
     * Tampilkan form untuk membuat barber baru.
     */
    public function create()
    {
        return view('admin.barbers.create');
    }

    /**
     * Simpan barber baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:8',
            'phone_number' => 'nullable|string|max:20',
            'experience' => 'nullable|string',
            'photo'      => 'nullable|image|max:2048',  // max 2MB
            'status'     => 'required|in:aktif,libur,cuti,nonaktif',
        ]);

        // Upload foto jika ada
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('barbers', 'public');
        }

        // Buat user baru (untuk login barber)
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'barber',                  // role otomatis barber
            'phone_number' => $validated['phone_number'] ?? null,
        ]);

        // Buat data barber yang terhubung ke user tersebut
        Barber::create([
            'user_id'    => $user->id,
            'experience' => $validated['experience'] ?? null,
            'photo'      => $photoPath,
            'status'     => $validated['status'],
        ]);

        // Redirect ke halaman daftar barber dengan pesan sukses
        return redirect()->route('admin.barbers.index')
                         ->with('success', 'Barber berhasil ditambahkan.');
    }

   
    public function edit(Barber $barber)
    {

        $barber->load('user');
        return view('admin.barbers.edit', compact('barber'));
    }


    public function update(Request $request, Barber $barber)
    {
        // Validasi, email harus unik kecuali untuk user yang sedang diedit
        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email,' . $barber->user_id,
            'phone_number' => 'nullable|string|max:20',
            'experience' => 'nullable|string',
            'photo'      => 'nullable|image|max:2048',
            'status'     => 'required|in:aktif,libur,cuti,nonaktif',
            'password'   => 'nullable|string|min:8',  // opsional
        ]);

        // Update data user 
        $barber->user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'] ?? null,
        ]);

        // Update password hanya jika diisi
        if (!empty($validated['password'])) {
            $barber->user->update(['password' => Hash::make($validated['password'])]);
        }

        // Update foto jika ada file baru
        if ($request->hasFile('photo')) {
            $barber->update(['photo' => $request->file('photo')->store('barbers', 'public')]);
        }

        // Update data barber (experience, status)
        $barber->update([
            'experience' => $validated['experience'] ?? $barber->experience,
            'status'     => $validated['status'],
        ]);

        return redirect()->route('admin.barbers.index')
                         ->with('success', 'Barber berhasil diperbarui.');
    }

    
    public function destroy(Barber $barber)
    {
        // Hapus user akan otomatis menghapus barber 
        $barber->user->delete();

        return redirect()->route('admin.barbers.index')
                         ->with('success', 'Barber berhasil dihapus.');
    }
}