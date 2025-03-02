<?php

use App\Models\FileAccess;
use App\Models\Files;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

test('Login failed', function () {
    $this->getJson('/files/eErd')->assertStatus(403)->assertJson([
        'message' => 'Login failed',
    ]);

    $this->deleteJson('/files/asdf')->assertStatus(403)->assertJson([
        'message' => 'Login failed',
    ]);

    $this->patchJson('/files/asdf')->assertStatus(403)->assertJson([
        'message' => 'Login failed',
    ]);

    $this->postJson('/files')->assertStatus(403)->assertJson([
        'message' => 'Login failed',
    ]);

    $this->getJson('/files/disk')->assertStatus(403)->assertJson([
        'message' => 'Login failed',
    ]);

    $this->getJson('/files/shared')->assertStatus(403)->assertJson([
        'message' => 'Login failed',
    ]);

    $this->getJson('/logout')->assertStatus(403)->assertJson([
        'message' => 'Login failed',
    ]);

    $this->postJson('/files/324/access')->assertStatus(403)->assertJson([
        'message' => 'Login failed',
    ]);

    $this->deleteJson('/files/324/access')->assertStatus(403)->assertJson([
        'message' => 'Login failed',
    ]);
});

test('Not Found 404', function () {
    $this->getJson('/')
        ->assertStatus(404)
        ->assertJson([
            'message' => 'Not Found',
        ]);

    $this->getJson('/api')
        ->assertStatus(404)
        ->assertJson([
            'message' => 'Not Found',
        ]);
});

test('Все таблицы есть в базе', function () {
    $user = User::all()->first();
    $file = Files::all()->first();
    $access_file = FileAccess::all()->first();
    $this->assertModelExists($user);
    $this->assertModelExists($file);
    $this->assertModelExists($access_file);
});

test('Присутствует тестовый пользователь test@gmail.com', function () {
    $this->assertDatabaseHas('users', [
        'email' => 'test@gmail.com'
    ]);
});

test('Добавить и удалить файл', function () {
    $user = User::where('email', 'test@gmail.com')->first();
    Sanctum::actingAs($user);
    Storage::fake('uploads');

    $file1 = UploadedFile::fake()->image('avatar.jpg');
    $file2 = UploadedFile::fake()->image('avatar.pdf');
    $response = $this->postJson('/files', [
        'files' => [
            $file1,
            $file2
        ],
    ]);

    Storage::disk('uploads')->assertExists("$user->id/avatar.jpg");
    $response->assertStatus(200);

    $file = Files::where('user_id', $user->id)->first();
    $response = $this->deleteJson("/files/$file->file_id");
    $response->assertStatus(200);

    Storage::disk('uploads')->delete("$user->id/avatar.jpg");
    $file->delete();
    Storage::disk('uploads')->assertMissing("$user->id/avatar.jpg");
});

