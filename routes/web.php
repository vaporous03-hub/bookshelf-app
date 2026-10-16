<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\RankingController;

Route::get('/', [BookController::class, 'index'])->name('books.index');

Route::resource('books', BookController::class)->only(['index', 'show']);

Route::get('/ranking', [RankingController::class, 'index'])->name('ranking.index');


Route::middleware(['auth'])->group(function () {

   
    Route::resource('books', BookController::class)->except(['index', 'show']);

    Route::resource('genres', GenreController::class);

   
    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::resource('reviews', ReviewController::class)->only(['edit', 'update', 'destroy']);
    Route::post('/reviews/{review}/like', [ReviewController::class, 'like'])->name('reviews.like'); 

    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/books/{book}/favorites', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/books/{book}/favorites', [FavoriteController::class, 'destroy'])->name('favorites.destroy');

});