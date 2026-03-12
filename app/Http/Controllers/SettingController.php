<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'app_name'            => 'nullable|string|max:255',
            'school_name'         => 'nullable|string|max:255',
            'copyright'           => 'nullable|string|max:255',
            'bank_name'           => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name'   => 'nullable|string|max:255',
            'teaching_rate_per_hour' => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'logo'                => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only([
            'app_name', 'school_name', 'copyright',
            'bank_name', 'bank_account_number', 'bank_account_name',
            'teaching_rate_per_hour', 'transport_allowance'
        ]);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('public/logos');
            $url = Storage::url($path);
            Setting::updateOrCreate(['key' => 'logo_url'], ['value' => $url]);
        }

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui.');
    }

    public function destroyLogo()
    {
        $setting = Setting::where('key', 'logo_url')->first();
        if ($setting) {
            $url = $setting->value;
            $path = str_replace('/storage/', 'public/', $url);
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
            $setting->delete();
        }

        return redirect()->back()->with('success', 'Logo berhasil dihapus.');
    }
}
