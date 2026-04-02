<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function index(): View|RedirectResponse
    {
        return redirect()->route('admin.dashboard')->with('error', 'Chức năng banner chưa được triển khai.');
    }

    public function create(): View|RedirectResponse
    {
        return redirect()->route('admin.dashboard')->with('error', 'Chức năng banner chưa được triển khai.');
    }

    public function store(Request $request): RedirectResponse
    {
        return redirect()->route('admin.dashboard')->with('error', 'Chức năng banner chưa được triển khai.');
    }

    public function show(string $id): RedirectResponse
    {
        return redirect()->route('admin.dashboard')->with('error', 'Chức năng banner chưa được triển khai.');
    }

    public function edit(string $id): RedirectResponse
    {
        return redirect()->route('admin.dashboard')->with('error', 'Chức năng banner chưa được triển khai.');
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        return redirect()->route('admin.dashboard')->with('error', 'Chức năng banner chưa được triển khai.');
    }

    public function destroy(string $id): RedirectResponse
    {
        return redirect()->route('admin.dashboard')->with('error', 'Chức năng banner chưa được triển khai.');
    }

    public function trash(): RedirectResponse
    {
        return redirect()->route('admin.dashboard')->with('error', 'Chức năng banner chưa được triển khai.');
    }

    public function restore(string $id): RedirectResponse
    {
        return redirect()->route('admin.dashboard')->with('error', 'Chức năng banner chưa được triển khai.');
    }

    public function forceDelete(string $id): RedirectResponse
    {
        return redirect()->route('admin.dashboard')->with('error', 'Chức năng banner chưa được triển khai.');
    }
}

