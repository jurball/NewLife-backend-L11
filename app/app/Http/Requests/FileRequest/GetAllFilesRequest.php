<?php

namespace App\Http\Requests\FileRequest;

use App\Models\Files;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class GetAllFilesRequest extends FormRequest
{
    public function foreach_files($data, $fill, $url, $owner): array
    {
        $response = [];
        foreach ($data as $file) {
            $get = Files::find($file->file_id);
            $user = User::find($file->user_id);

            $response[] =
                [
                    'file_id' => $get->file_id,
                    'name' => $get->original_name,
                    'url' => $url . '/files/' . $get->file_id,
                    'access' => [
                        [
                            'fullname' => $owner->first_name . ' ' . $owner->last_name,
                            'email' => $owner->email,
                            'type' => 'author'
                        ],
                        [
                            'fullname' => $user->first_name . ' ' . $user->last_name,
                            'email' => $user->email,
                            'type' => 'co-author'
                        ]
                    ]
                ];
        }

        foreach ($fill as $f) {
            $response[] = [
                'file_id' => $f->file_id,
                'name' => $f->original_name,
                'url' => $url . '/files/' . $f->file_id,
                'access' => []
            ];
        }

        return $response;
    }
}
