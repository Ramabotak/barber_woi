<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SettingController extends Controller
{
    protected const CACHE_KEY = 'barber_woi_settings';

    public function edit(): View
    {
        $settings = Cache::get(self::CACHE_KEY, [
            'opening_time' => '09:00',
            'closing_time' => '21:00',
            'late_tolerance_minutes' => 15,
            'shop_name' => 'Barber Woi',
            'shop_address' => '',
            'shop_phone' => '',
        ]);

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'opening_time' => ['required', 'date_format:H:i'],
            'closing_time' => ['required', 'date_format:H:i', 'after:opening_time'],
            'late_tolerance_minutes' => ['required', 'integer', 'min:0'],
            'shop_name' => ['required', 'string', 'max:255'],
            'shop_address' => ['nullable', 'string', 'max:500'],
            'shop_phone' => ['nullable', 'string', 'max:20'],
        ]);

        Cache::forever(self::CACHE_KEY, $validated);

        return redirect()->route('admin.settings.edit')->with('success', 'Pengaturan berhasil disimpan.');
    }
}
