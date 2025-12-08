<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TestAuthController extends Controller
{
    public function getTestToken()
    {
        $user = User::firstOrCreate(
            ['email' => 'test@taskflow.test'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        // Створюємо токен
        $token = $user->createToken('test-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'instructions' => [
                '1. Скопіюйте токен вище',
                '2. Відкрийте консоль браузера на сторінці /live',
                '3. Виконайте: localStorage.setItem("token", "ВАШ_ТОКЕН")',
                '4. Оновіть сторінку',
            ],
        ]);
    }

    public function createTestProject()
    {
        $user = User::first();

        if (!$user) {
            return response()->json(['error' => 'Користувачів не знайдено'], 404);
        }

        $project = \App\Models\Project::firstOrCreate(
            ['id' => 7],
            [
                'name' => 'Тестовий проєкт для WebSocket',
                'owner_id' => $user->id,
                'description' => 'Проєкт для тестування підключень WebSocket',
            ]
        );

        $project->users()->syncWithoutDetaching([
            $user->id => ['role' => 'owner']
        ]);

        return response()->json([
            'project' => $project,
            'message' => 'Проєкт з ID 7 створено або оновлено',
        ]);
    }
}
