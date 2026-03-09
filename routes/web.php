<?php

use App\Http\Controllers\PassportController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('posts.index');
});

Route::resource('posts', PostController::class);

Route::post('/posts/process', [PassportController::class, 'process'])->name('posts.process');
