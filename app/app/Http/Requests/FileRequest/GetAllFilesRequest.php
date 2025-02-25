<?php

namespace App\Http\Requests\FileRequest;

use Illuminate\Foundation\Http\FormRequest;

class GetAllFilesRequest extends FormRequest
{
    public function foreach_files($data, $url): array
    {
        $response = [];
        foreach ($data as $file) {
            $response[] = [
                'file_id' => $file->file_id,
                'name' => $file->original_name,
                'url' => $url . '/files/' . $file->file_id,
                'access' => [],
            ];
        }

        return $response;
    }
}
