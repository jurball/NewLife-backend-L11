<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\files;

class FileController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:png,jpg,jpeg,gif|max:2048', // Пример для изображений
        ]);

        if (!$request->hasFile('file')) {
            return response()->json(['message' => 'No file uploaded'], 400);
        }

        $file = $request->file('file');

        if (!$file->isValid()) {
            return response()->json(['message' => 'File is not valid'], 400);
        }

        $extension = $file->getClientOriginalExtension();
        $originalName = $file->getClientOriginalName();

        // проверка на существуюищй файл
        if(Storage::path('files/' . $originalName)){
            // Если файл с таким именем существует, добавляем (i) к имени
            $name = pathinfo(Storage::path('files/' . $originalName), PATHINFO_FILENAME);
            $i = 1;
            $newName = $name . ' (' . $i . ').' . $extension;

            // Проверяем, существует ли уже такой файл с индексом
            while (files::where('original_name', $newName)->exists()) {
                $i++;
                $newName = $name . ' (' . $i . ').' . $extension;
            }
        } else {
            $newName = $originalName;
        }
/*
 *      Генерация уникального имени для файла
 *      $filePath = $file->store('files', 'local');
*/
        $filePath = $file->storeAs('files', $newName, 'local');



        // Проверка на зарегитрированного пользователя
        $user = Auth::guard('sanctum');
        if($user->check()) {

        }

        // Создание записи в базе данных о файле
        files::create([
            'user_id' => 1,
            'path' => $filePath,
            'original_name' => $newName,
        ]);

        return response()->json([
            'message' => 'File uploaded successfully',
            'data_test' => [
                'extension'=> $extension,
                'name' => $originalName,
                'path' => $filePath,
                'size' => filesize($file),
                'url' => Storage::url($filePath),
                'phpinfo' => pathinfo($filePath, PATHINFO_FILENAME),
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function getFile($fileId)
    {
/*
        $file = Auth::user()->files()->findOrFail($fileId);
        return response()->download(storage_path("app/public/{$file->path}"));
*/
//        return Storage::download('files/peLHfpmDpeEpJw0T5TMB5vAfvf2yfvwyOI9pyQoQ.png');
/*        return response()->json([
            'sd' => Storage::path('files/peLHfpmDpeEpJw0T5TMB5vAfvf2yfvwyOI9pyQoQ.png') полный патч, не url
        ]);*/
        return response()->json([
            'sd' => Storage::path('files/peLHfpmDpeEpJw0T5TMB5vAfvf2yfvwyOI9pyQoQ.png')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateFile(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteFile(string $id)
    {
        //Storage::delete('files/fgdfgs.png');
        //
    }
}
