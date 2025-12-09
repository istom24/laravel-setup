<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;

Route::redirect('/', '/test');

Route::get('/test', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Додаток працює',
        'time' => now()->toDateTimeString(),
        'broadcast_driver' => config('broadcasting.default'),
        'reverb_config' => config('reverb')
    ]);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/live', function () {
        return view('live');
    })->name('live');
});


