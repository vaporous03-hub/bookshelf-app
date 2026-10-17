<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\RankingController;

Route::get('/', [BookController::class, 'index'])->name('books.index');
Route::get('/ranking', [RankingController::class, 'index'])->name('ranking.index');
Route::get('/books', [BookController::class, 'index'])->name('books.index');


Route::middleware(['auth'])->group(function () {

   
    Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
    Route::post('/books', [BookController::class, 'store'])->name('books.store');

    Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');

    Route::resource('genres', GenreController::class);

   
    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::resource('reviews', ReviewController::class)->only(['edit', 'update', 'destroy']);
    Route::post('/reviews/{review}/like', [ReviewController::class, 'like'])->name('reviews.like'); 

    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/books/{book}/favorites/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::post('/books/{book}/favorites', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::delete('/books/{book}/favorites', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
    Route::post('/books/{book}/favorites/store', [FavoriteController::class, 'store'])->name('favorites.store');
    
    Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');
});