<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Tampilkan daftar semua layanan.
     */
    public function index()
    {
        $services = Service::latest()->paginate(10);
        return view('admin.services.index', compact('services'));
    }

    /**
     * Form tambah layanan baru.
     */
    public function create()
    {
        return view('admin.services.create');
    }

    /**
     * Simpan layanan baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_name' => 'required|string|max:100',
            'price'        => 'required|numeric|min:0',
            'duration'     => 'required|integer|min:1',
            'description'  => 'nullable|string',
            'status'       => 'required|in:active,inactive',
        ]);

        Service::create($validated);

        return redirect()->route('admin.services.index')
                         ->with('success', 'Layanan berhasil ditambahkan.');
    }

    /**
     * Form edit layanan.
     */
    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    /**
     * Perbarui layanan.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'service_name' => 'required|string|max:100',
            'price'        => 'required|numeric|min:0',
            'duration'     => 'required|integer|min:1',
            'description'  => 'nullable|string',
            'status'       => 'required|in:active,inactive',
        ]);

        $service->update($validated);

        return redirect()->route('admin.services.index')
                         ->with('success', 'Layanan berhasil diperbarui.');
    }

    /**
     * Hapus layanan.
     */
    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()->route('admin.services.index')
                         ->with('success', 'Layanan berhasil dihapus.');
    }
}