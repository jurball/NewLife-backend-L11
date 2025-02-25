<?php

namespace App\Http\Requests\FileRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;

class DeleteFileRequest extends FormRequest
{
    public function delete($filepath, $file): bool
    {
        $disk = 'uploads';
        if (Storage::disk($disk)->exists($filepath)) {
            // Очистить из диска и базы данных
            Storage::disk($disk)->delete($filepath);
            $file->delete();
            return true;
        }

        return false;
    }
}
