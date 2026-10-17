<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    
    public function index(Request $request)
    {
        
        $books = $request->user()
            ->favoriteBooks()
            ->with(['genres'])
            ->latest('favorites.created_at')
            ->paginate(10);

        return view('favorites.index', compact('books'));
    }

    public function store(Book $book)
    {
        
        Auth::user()->favoriteBooks()->syncWithoutDetaching([$book->id]);

        return back()->with('success', 'お気に入りに追加しました。');
    }

    public function destroy(Book $book)
    {
       
        Auth::user()->favoriteBooks()->detach($book->id);

        return back()->with('success', 'お気に入りを解除しました。');
    }
    public function toggle(Request $request, Book $book)
    {
        
        $status = $request->user()->favoriteBooks()->toggle($book->id);

        $isAttached = count($status['attached']) > 0;
        $message = $isAttached ? 'お気に入りに追加しました。' : 'お気に入りを解除しました。';

        return back()->with('status', $message);
    }
}
