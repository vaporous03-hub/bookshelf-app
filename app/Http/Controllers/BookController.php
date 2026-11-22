<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;

class BookController extends Controller
{

    public function index(Request $request)
    {
        $query = Book::with(['genres'])
            ->withAvg('reviews', 'rating')
            ->withExists(['favoritedBy as is_favorited' => function ($query) {
                $query->where('user_id', auth()->id());
            }])
           ;

            if ($request->filled('keyword')) {
            $keyword = $request->input('keyword');
            $query->where(function ($query) use ($keyword) {
                $query->where('title', 'like', "%{$keyword}%")
                  ->orWhere('author', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('genre')) {
            $genreId = $request->input('genre');
            $query->whereHas('genres', function ($query) use ($genreId) {
                $query->where('genres.id', $genreId);
            });
        }

        $sort = $request->input('sort', 'latest');
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            case 'rating':

                $query->orderByRaw('reviews_avg_rating IS NULL ASC')
                      ->orderBy('reviews_avg_rating', 'desc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }
        $books = $query->paginate(10)->withQueryString();
        $genres = Genre::all();

        return view('books.index', compact('books', 'genres', 'sort'));
    }


    public function create()
    {

        $genres = Genre::all();

        return view('books.create', compact('genres'));
    }


    public function store(StoreBookRequest $request)
    {
        
        $validated = $request->validated();
        $validated['user_id'] = Auth::id();
        $book = Book::create($validated);
        $book->genres()->attach($request->input('genres'));
        
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
        $book->genres()->sync($request->input('genres'));

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
