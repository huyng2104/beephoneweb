<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Storage;
class FileHelper
{
    public static function upload($file, $folder = 'uploads')
    {
        if (!$file) {
            return null;
        }

        $fileName = time() . '_' . $file->getClientOriginalName();

        return $file->storeAs($folder, $fileName, 'public');
    }
    public static function delete($path)
    {
        if (!$path) return;

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
