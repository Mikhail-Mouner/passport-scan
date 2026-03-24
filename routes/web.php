<?php

use App\Http\Controllers\PassportController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('posts.index');
});

Route::resource('posts', PostController::class);
Route::get('/scan', function () {
    return view('scan');
})->name('scan');

Route::post('/upload-scanned-image', [PostController::class, 'upload']);
Route::post('/posts/process', [PassportController::class, 'process'])->name('posts.process');
