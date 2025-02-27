<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\FileRequest\Access\AddAccessFileRequest;
use App\Http\Requests\FileRequest\Access\DeleteAccessFileRequest;
use App\Http\Requests\FileRequest\DeleteFileRequest;
use App\Http\Requests\FileRequest\GetAllFilesRequest;
use App\Http\Requests\FileRequest\SharedRequest;
use App\Http\Requests\FileRequest\UpdateNameFileRequest;
use App\Http\Requests\FileRequest\UploadFileRequest;
use App\Models\FileAccess;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Files;

/**
 * @group Авторизованный пользователь
 *
 * APIs для работы с файлами (для авторизованных пользователей)
 */
class FileController
{
    /**
     * GET files/disk
     *
     * Получить все файлы
     * @response 200 [
     * {
     * "file_id": "qweasd1234",
     * "name": "Имя файла",
     * "url": "{{host}}/files/qweasd1234",
     * "accesses": [
     * {
     * "fullname": "name last_name",
     * "email": "admin@admin.ru",
     * "type": "author"
     * },
     * {
     * "fullname": "user last_name",
     * "email": "user@user.ru",
     * "type": "co-author"
     * }
     * ]
     * },
     * {
     * "file_id": "aaaaaaaaaa",
     * "name": "Имя файла 1",
     * "url": "{{host}}/files/aaaaaaaaaa",
     * "accesses": [
     * {
     * "fullname": "name last_name",
     * "email": "admin@admin.ru",
     * "type": "author"
     * }
     * ]
     * }
     * ]
     *
     */
    public function getAllFiles(GetAllFilesRequest $request): JsonResponse
    {
        $fill = User::find(Auth::id())->files;
        $data = User::find(Auth::id())->owner_files;
        $owner = User::find(Auth::id());

        $response = $request->foreach_files($data, $fill, $request->getSchemeAndHttpHost(), $owner);

        return response()->json($response ?? []);
    }

    /**
     * GET files/{fileId}
     *
     * Получить файл по {fileId}
     *
     * @response 200 Браузеру отдается файл
     */
    public function downloadFile($fileId)
    {
        $file = Files::where('file_id', $fileId)->firstOrFail();
        $path = Storage::path($file->path);

        return response()->download($path);
    }

    /**
     * @response 200 {
     * "success": true,
     * "message": "File already deleted"
     * }
     */
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

    /**
     * @response 200 [
     * {
     * "success": true,
     * "message": "Success",
     * "name": "Имя загруженного файла",
     * "url": "{{host}}/files/qweasd1234",
     * "file_id": "qweasd1234"
     * },
     * {
     * "success": false,
     * "message": "File not loaded",
     * "name": "Имя НЕ загруженного файла"
     * }
     * ]
     */
    public function uploadFile(UploadFileRequest $request): JsonResponse
    {
        $responses = $request->files_array_iterate($request->file('files'), $request->getSchemeAndHttpHost(), Auth::id());
        return response()->json($responses);
    }

    /**
     * @response 200 {
     * "success": true,
     * "message": "Renamed"
     * }
     */
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

    /**
     * @response 200 [
     * {
     * "fullname": "name last_name",
     * "email": "admin@admin.ru",
     * "type": "author"
     * },
     * {
     * "fullname": "user last_name",
     * "email": "user@user.ru",
     * "type": "co-author"
     * }
     * ]
     */

    public function addAccessFile($fileId, AddAccessFileRequest $request): JsonResponse
    {
        $owner = Auth::user();

        $add_user = User::where('email', $request->get('email'))->firstOrFail();
        $file_id = Files::where('file_id', $fileId)->firstOrFail()->id;

        $fullname_user = $add_user->first_name . ' ' . $add_user->last_name;
        $fullname_owner = $owner->first_name . ' ' . $owner->last_name;

        if (
            FileAccess::where('file_id', $file_id)->exists() &&
            FileAccess::where('user_id', $add_user->id)->exists()
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Access already granted'
            ], 409);
        }

        FileAccess::create([
            'file_id' => $file_id,
            'user_id' => $add_user->id,
            'owner_id' => Auth::id(),
        ]);

        return response()->json([
            [
                "fullname" => $fullname_owner,
                "email" => $owner->email,
                "type" => "author"
            ],
            [
                "fullname" => $fullname_user,
                "email" => $add_user->email,
                "type" => "co-author"
            ]
        ]);
    }

    /**
     * @response 200 [
     * {
     * "fullname": "name last_name",
     * "email": "admin@admin.ru",
     * "type": "author"
     * }
     * ]
     */
    public function deleteAccessFile(DeleteAccessFileRequest $request, $fileId): JsonResponse
    {
        $owner = Auth::user();
        $fullname_owner = $owner->first_name . ' ' . $owner->last_name;

        $add_user = User::where('email', $request->get('email'))->firstOrFail();
        $file_id = Files::where('file_id', $fileId)->firstOrFail()->id;

        if (
            !FileAccess::where('file_id', $file_id)->exists() &&
            !FileAccess::where('user_id', $add_user->id)->exists()
        ) {
            return response()->json([
                'message' => 'Not found'
            ], 404);
        }

        FileAccess::where('file_id', $file_id)->firstOrFail()->delete();

        return response()->json([
            [
                "fullname" => $fullname_owner,
                "email" => $owner->email,
                "type" => "author"
            ]
        ]);
    }

    /**
     * @response 200 [
     * {
     * "file_id": "qweasd1234",
     * "name": "Имя файла",
     * "url": "{{host}}/files/qweasd1234"
     * },
     * {
     * "file_id": "aaaaaaaaaa",
     * "name": "Имя файла 2",
     * "url": "{{host}}/files/aaaaaaaaaa"
     * }
     * ]
     */
    public function shared(SharedRequest $request)
    {
        $data = User::find(Auth::id())->access_files;
        $responses = $request->response_shared_files($data, $request->getSchemeAndHttpHost());

        return response()->json($responses ?? []);
    }
}
