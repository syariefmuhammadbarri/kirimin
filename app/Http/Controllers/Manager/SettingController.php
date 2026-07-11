<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('key')->get();
        
        // Ensure $settings is always a Collection, not stdClass
        if (!$settings instanceof \Illuminate\Support\Collection) {
            $settings = collect($settings);
        }
        
        return view('manager.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string|max:100',
            'settings.*.value' => 'nullable|string|max:500',
        ]);

        foreach ($request->settings as $item) {
            Setting::updateOrCreate(
                ['key' => $item['key']],
                ['value' => $item['value'] ?? '']
            );
        }

        return redirect()->route('manager.settings.index')
            ->with('success', 'Pengaturan berhasil diperbarui.');
    }
}