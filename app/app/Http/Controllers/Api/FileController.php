<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\FileRequest\DeleteFileRequest;
use App\Http\Requests\FileRequest\GetAllFilesRequest;
use App\Http\Requests\FileRequest\UpdateNameFileRequest;
use App\Http\Requests\FileRequest\UploadFileRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Files;

class FileController
{
    public function getAllFiles(GetAllFilesRequest $request): JsonResponse
    {
        $data = User::find(Auth::id())->files;
        $response = $request->foreach_files($data, $request->getSchemeAndHttpHost());

        return response()->json($response ?? []);
    }

    public function downloadFile($fileId)
    {
        $file = Files::where('file_id', $fileId)->firstOrFail();
        $path = Storage::path($file->path);

        return response()->download($path);
    }

    public function deleteFile(DeleteFileRequest $request, string $fileId): JsonResponse
    {
        $file = Files::where('file_id', $fileId)->firstOrFail();
        $filepath = $file->path;

        if ($request->delete($filepath, $file)) {
            return response()->json([
                'success' => true,
                'message' => 'File already deleted'
            ]);
        }

        return response()->json([
            'message' => 'Bad request'
        ], 400);
    }

    public function uploadFile(UploadFileRequest $request): JsonResponse
    {
        $responses = $request->files_array_iterate($request->file('files'), $request->getSchemeAndHttpHost(), Auth::id());
        return response()->json($responses);
    }

    public function updateNameFile(UpdateNameFileRequest $request, $fileId): JsonResponse
    {
        // Инициализация
        $file = Files::where('file_id', $fileId)->firstOrFail();
        $request_filename = $request->get('name');

        $request->update_name_file($request_filename, $file);

        return response()->json([
            'success' => true,
            'message' => 'Renamed',
        ]);
    }

    public function addAccessFile()
    {
        return response()->json([]);
    }

    public function deleteAccessFile()
    {
        return response()->json([]);
    }

    public function shared()
    {
        return response()->json([
            [
                "file_id" => "aaaaaaaaaa",
                "name" => "Имя файла 2",
                "url" => "{{host}}/files/aaaaaaaaaa",
            ],
            [
                "file_id" => "qweasd1234",
                "name" => "Имя файла",
                "url" => "{{host}}/files/qweasd1234",
            ]
        ]);
    }
}
