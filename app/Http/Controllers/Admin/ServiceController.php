<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    /**
     * Tampilkan daftar semua layanan.
     */
    public function index()
    {
        $services = Service::latest()->paginate(12);

        // Data untuk 3 kartu statistik di atas grid layanan
        $activeCount   = Service::where('status', 'active')->count();
        $inactiveCount = Service::where('status', 'inactive')->count();
        $avgPrice      = Service::avg('price') ?? 0;

        return view('admin.services.index', compact('services', 'activeCount', 'inactiveCount', 'avgPrice'));
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
            'photo'        => 'nullable|image|max:2048',
            'price'        => 'required|numeric|min:0',
            'duration'     => 'required|integer|min:1',
            'description'  => 'nullable|string',
            'status'       => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('services', 'public');
        }

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
            'photo'        => 'nullable|image|max:2048',
            'price'        => 'required|numeric|min:0',
            'duration'     => 'required|integer|min:1',
            'description'  => 'nullable|string',
            'status'       => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('photo')) {
            // Hapus foto lama biar tidak menumpuk file yatim di storage
            if ($service->photo) {
                Storage::disk('public')->delete($service->photo);
            }
            $validated['photo'] = $request->file('photo')->store('services', 'public');
        }

        $service->update($validated);

        return redirect()->route('admin.services.index')
                         ->with('success', 'Layanan berhasil diperbarui.');
    }

    /**
     * Hapus layanan.
     */
    public function destroy(Service $service)
    {
        if ($service->photo) {
            Storage::disk('public')->delete($service->photo);
        }

        $service->delete();

        return redirect()->route('admin.services.index')
                         ->with('success', 'Layanan berhasil dihapus.');
    }
}