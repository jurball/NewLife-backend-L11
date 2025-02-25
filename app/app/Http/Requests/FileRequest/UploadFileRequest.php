<?php

namespace App\Http\Requests\FileRequest;

use App\Models\Files;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadFileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
//            'files' => 'required|array|mimes:png,jpg,jpeg,gif,pdf|max:2048',
            'files' => 'required|array',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $validator->errors()
        ], 422));
    }

    public function files_array_iterate($files, $url, $user_id): array
    {
        function newNameFile($file, $user_id, $originalName, $extension): string
        {
            $newName = $file->getClientOriginalName();
            $counter = 1;
            while (Storage::disk('uploads')->exists($user_id . '/' . $newName)) {
                $newName = $originalName . " ($counter)." . $extension;
                $counter++;
            }

            return $newName;
        }

        $responses = [];
        $first_file = true;
        foreach ($files as $file) {

            $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            if ($file->isValid() && $first_file) {
                $newName = newNameFile($file, $user_id, $originalName, $extension);
                $file_id = Str::random(10);

                $filePath = $file->storeAs("$user_id", $newName, 'uploads');

                Files::create([
                    'file_id' => $file_id,
                    'user_id' => $user_id,
                    'path' => $filePath,
                    'original_name' => $newName,
                ]);

                $responses[] = [
                    'success' => true,
                    'message' => 'File uploaded',
                    'name' => $newName,
                    'url' => $url . '/' . $file_id,
                    'file_id' => $file_id,
                ];
                $first_file = false;
            } else {
                $responses[] = [
                    'success' => false,
                    'message' => 'File not loaded',
                    'name' => $originalName . '.' . $extension,
                ];
            }
        }
        return $responses;
    }
}
