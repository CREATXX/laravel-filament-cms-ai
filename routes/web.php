<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ContactController;

// Ana Sayfa
Route::get('/', [PageController::class, 'home'])->name('home');

// Sayfa Detay
Route::get('/sayfa/{slug}', [PageController::class, 'show'])->name('page.show');

// Blog Route'ları
Route::prefix('blog')->group(function () {
    Route::get('/', [PostController::class, 'index'])->name('blog.index');
    Route::get('/kategori/{category}', [PostController::class, 'category'])->name('blog.category');
    Route::get('/{slug}', [PostController::class, 'show'])->name('blog.show');
});

// İletişim Formu
Route::post('/iletisim', [ContactController::class, 'submit'])->name('contact.submit');
