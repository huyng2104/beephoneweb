<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = \App\Models\Setting::all()->keyBy('key');
        $categories = \App\Models\Category::where('is_active', true)->whereNull('parent_id')->with('children')->get();
        $postCategories = \App\Models\PostCategory::all(); 
        $brands = \App\Models\Brand::all();
        return view('admin.settings.index', compact('settings', 'categories', 'postCategories', 'brands'));
    }

    public function update(Request $request)
    {
        // Cập nhật các thiết lập đơn lẻ (text, number, image path)
        if ($request->has('settings')) {
            $settingsData = $request->settings;
            
            // Handle file uploads for General Settings
            if ($request->hasFile('settings.site_logo')) {
                $path = $request->file('settings.site_logo')->store('settings', 'public');
                $settingsData['site_logo'] = '/storage/' . $path;
            }
            
            if ($request->hasFile('settings.site_favicon')) {
                $path = $request->file('settings.site_favicon')->store('settings', 'public');
                $settingsData['site_favicon'] = '/storage/' . $path;
            }

            foreach ($settingsData as $key => $value) {
                // Since maintenance_mode is now in settings_json, we make sure we don't save it as a flat setting
                if ($key === 'maintenance_mode') continue; 
                
                \App\Models\Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }

        // Cập nhật các thiết lập phức tạp (JSON) - Ví dụ: thanh toán, phí vận chuyển
        if ($request->has('settings_json')) {
            foreach ($request->settings_json as $key => $values) {
                \App\Models\Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $values]
                );
            }
        }

        return redirect()->route('admin.settings.index')->with('success', 'Đã lưu thiết lập hệ thống thành công!');
    }
}
