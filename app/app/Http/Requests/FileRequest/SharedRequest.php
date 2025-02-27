<?php

namespace App\Http\Requests\FileRequest;

use App\Models\Files;
use Illuminate\Foundation\Http\FormRequest;

class SharedRequest extends FormRequest
{
    public function response_shared_files($data, $url): array
    {
        $responses = [];
        foreach ($data as $file) {
            $response = Files::find($file['file_id']);
            $responses[] = [
                'file_id' => $response->file_id,
                'name' => $response->original_name,
                'url' => $url . '/files/' . $response->file_id,
            ];
        }
        return $responses;
    }
}
