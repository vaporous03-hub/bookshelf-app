<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Http\Requests\ApiStoreBookRequest;
use App\Http\Requests\ApiUpdateBookRequest;
use App\Http\Resources\BookResource;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $books = Book::with('genres')->latest()->paginate(10);
        return BookResource::collection($books);
    }

    public function show(Book $book)
    {
        $book->load(['user', 'genres', 'reviews']);
        return new BookResource($book);
    }

    public function store(ApiStoreBookRequest $request)
    {
        $validated = $request->validated();
        $book = Book::create($validated);
        
        if ($request->has('genre_ids')) {
            $book->genres()->attach($request->input('genre_ids'));
        }

        return (new BookResource($book))
            ->additional(['message' => '書籍を登録しました。'])
            ->response()
            ->setStatusCode(201);
    }

    public function update(ApiUpdateBookRequest $request, Book $book)
    {
        $validated = $request->validated();
        $book->update($validated);
        
        if ($request->has('genre_ids')) {
            $book->genres()->sync($request->input('genre_ids'));
        }

        return new BookResource($book);
    }

    public function destroy(Book $book)
    {
        $book->delete();
        return response()->json(null, 204);
    }
}