<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\files;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use function PHPSTORM_META\map;

class FileController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::guard('sanctum');
        $data = files::where('user_id', $user->id())->get();

        foreach ($data as $file) {
            $response[] = [
                'file_id' => $file->ids,
                'name' => $file->original_name,
                'url' => $request->getSchemeAndHttpHost() . '/files/' . $file->ids,
                'access' => [],
            ];
        }

        return response()->json($response ?? []);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function uploadFile(Request $request)
    {
        $user = Auth::guard('sanctum');
        $user_id = $user->id();

        // Файл был загружен?
        if (!$request->hasFile('files')) {
            return response()->json(['message' => 'No file uploaded'], 400);
        }

        // Валидация входных данных
        $validator = Validator::make($request->all(), [
            'files' => 'required|file|mimes:png,jpg,jpeg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()
            ], 422);
        }

        $file = $request->file('files');
        $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION); // Расширение
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        // проверяет, был ли файл успешно загружен (без ошибок).
        if (!$file->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'File not loaded',
                'name' => $originalName,
            ]);
        }

        $newName = $file->getClientOriginalName();
        $counter = 1;
        while (Storage::disk('uploads')->exists($user_id . '/' . $newName)) {
            $newName = $originalName . " ($counter)." . $extension;
            $counter++;
        }

        $filePath = $file->storeAs("$user_id", $newName, 'uploads');

        // Создание записи в базе данных о файле
        $file_id = Str::random(10);
        files::create([
            'ids' => $file_id,
            'user_id' => $user_id,
            'path' => $filePath,
            'original_name' => $newName,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Success',
            'url' => $request->getSchemeAndHttpHost() . '/files/' . $file_id,
            'file_id' => $file_id,
            'data_test' => [
                'extension'=> $extension,
                'name' => $originalName,
                'path' => $filePath,
                'size' => filesize($file),
                'url' => Storage::path($filePath),
                'phpinfo' => pathinfo($filePath, PATHINFO_FILENAME),
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function getFile($fileId)
    {
        return;
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateFile(Request $request, string $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteFile(string $fileId)
    {
        $disk = 'uploads';
        $user = Auth::guard('sanctum');

        // Проверяем, существует ли файл
        try {
            $file = files::where('ids', $fileId)->first();
            if ($file->user_id !== $user->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden',
                ], 403);
            }

            $filename = $file->path;
            if (Storage::disk($disk)->exists($filename)) {
                Storage::disk($disk)->delete($filename);
                $file->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'File already deleted'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Файл не существует'
            ], 404);
        }

        return response()->json([
            'message' => 'Bad request'
        ], 400);
    }
}
