<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;

class BookController extends Controller
{

    public function index()
    {
        $books = Book::with(['genres'])
            ->withExists(['favoritedBy as is_favorited' => function ($query) {
                $query->where('user_id', auth()->id());
            }])
            ->latest() 
            ->paginate(10);

        return view('books.index', compact('books'));
    }


    public function create()
    {

        $genres = Genre::all();

        return view('books.create', compact('genres'));
    }


    public function store(StoreBookRequest $request)
    {
        $validated['user_id'] = Auth::id();

        $book = Book::create($validated);

        $book->genres()->attach($request->input('genre_ids'));


        return redirect()
            ->route('books.show', $book)
            ->with('success', '書籍を登録しました。');
    }


    public function show(Book $book)
    {
      
        $book->load(['user', 'genres', 'reviews.user', 'reviews.likedByUsers']);

        return view('books.show', compact('book'));
    }


    public function edit(Book $book)
    {
        
        if ($book->user_id !== Auth::id()) {
            return redirect()
                ->route('books.show', $book)
                ->with('abort', '作成者以外の書籍は編集できません。');
        }

        $genres = Genre::all();
        return view('books.edit', compact('book', 'genres'));
    }

    public function update(UpdateBookRequest $request, Book $book)
    {
        if ($book->user_id !== Auth::id()) {
            return redirect()
                ->route('books.show', $book)
                ->with('abort', '作成者以外の書籍は編集できません。');
        }

        $validated = $request->validated();
        $book->update($validated);
        $book->genres()->sync($request->input('genre_ids'));

        return redirect()
            ->route('books.show', $book)
            ->with('success', '書籍情報を更新しました。');
    }

    public function destroy(Book $book)
    {
        if ($book->user_id !== Auth::id()) {
            return redirect()
                ->route('books.show', $book)
                ->with('abort', '作成者以外の書籍は削除できません。');
        }

        $book->delete();

        return redirect()
            ->route('books.index')
            ->with('success', '書籍を削除しました。');
    }
}
