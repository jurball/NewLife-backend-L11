<?php

namespace App\Http\Requests\FileRequest;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Storage;

class UpdateNameFileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'string|required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $validator->errors(),
        ], 422));
    }

    public function update_name_file($request_filename, $file): bool
    {
        $extension = pathinfo($file->path, PATHINFO_EXTENSION);
        $newName = $request_filename . '.' . $extension;
        $newPath = $file->user_id.'/'.$newName;

        // установить счетчик
        $counter = 1;
        while (Storage::disk('uploads')->exists($newPath)) {
            $newName = $request_filename . " ($counter)." . $extension;
            $newPath = $file->user_id.'/'.$newName;
            $counter++;
        }

        Storage::move($file->path, $newPath);

        $file->update([
            'original_name' => $newName,
            'path' =>  $newPath
        ]);

        return true;
    }
}
