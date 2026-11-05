<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Http\Requests\StoreGenreRequest;
use App\Http\Requests\UpdateGenreRequest;


class GenreController extends Controller
{
    
    public function index()
    {
       $genres = Genre::withCount('books')->get();

        return view('genres.index', compact('genres')); 
    }
    public function create()
    {
        return view('genres.create');
    }

    public function store(StoreGenreRequest $request)
    {
        Genre::create($request->validated());

        return redirect()->route('genres.index')->with('success', 'ジャンルを登録しました。');
    }

    public function show(Genre $genre)
    {
        $books = $genre->books()
            ->with(['genres'])
            ->withExists(['favoritedBy as is_favorited' => function ($query) {
                $query->where('user_id', auth()->id());
            }])
            ->latest()
            ->paginate(10);

        return view('genres.show', compact('genre', 'books'));    }

    public function edit(Genre $genre)
    {
        return view('genres.edit', compact('genre'));
    }

    public function update(UpdateGenreRequest $request, Genre $genre)
    {
        $genre->update($request->validated());

        return redirect()
            ->route('genres.index')
            ->with('success', 'ジャンル情報を更新しました。');
    }

    public function destroy(Genre $genre)
    {   
        if ($genre->books()->exists()) {
            
            return redirect()
                ->route('genres.index')
                ->with('abort', 'このジャンルは書籍に使用されているため削除できません。');
        }
       $genre->delete();

        return redirect()
            ->route('genres.index')
            ->with('success', 'ジャンルを削除しました。');
    }
}
