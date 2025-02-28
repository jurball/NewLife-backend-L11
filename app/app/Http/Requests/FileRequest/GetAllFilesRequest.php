<?php

namespace App\Http\Requests\FileRequest;

use App\Models\Files;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class GetAllFilesRequest extends FormRequest
{
    public function foreach_files($data, $url, $owner_author): array
    {
        $response = [];
        foreach ($data as $file) {
            $find = [];
            $access_file = $file->access_file;

            foreach ($access_file as $a) {
                $user_id = User::find($a->user_id) ?? [];
                $find[] = [
                    'fullname' => $user_id->first_name . ' ' . $user_id->last_name,
                    'email' => $user_id->email,
                    'type' => 'co-author',
                ];
            }

            if(empty($find)){
                $response[] = [
                    'file_id' => $file->file_id,
                    'name' => $file->original_name,
                    'url' => $url . '/files/'  . $file->file_id,
                    'accesses' => [],
                ];
            } else {
                array_unshift($find, $owner_author);
                $response[] = [
                    'file_id' => $file->file_id,
                    'name' => $file->original_name,
                    'url' => $url . '/files/'  . $file->file_id,
                    'accesses' => $find,
                ];
            }
        }

        return $response;
    }
}
