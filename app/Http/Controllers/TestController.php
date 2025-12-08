<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'TestController работает!',
            'data' => [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'time' => now()->toDateTimeString(),
            ]
        ]);
    }
}
