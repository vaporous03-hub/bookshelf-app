<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    
    public function store(StoreReviewRequest $request, Book $book)
    {

        $validated = $request->validated();
        
        $validated['user_id'] = Auth::id();
        $validated['book_id'] = $book->id;
        
        Review::create($validated);

        return redirect()
        ->route('books.show', $book)
        ->with('success', 'レビューを投稿しました。');
    }


    public function edit(Review $review)
    {
        
        if ($review->user_id !== Auth::id()) {
            return redirect()
                ->route('books.show', $review->book_id)
                ->with('abort', '投稿者以外のレビューは編集できません。');
        }

        return view('reviews.edit', compact('review'));
    }

    public function update(UpdateReviewRequest $request, Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            return redirect()
                ->route('books.show', $review->book_id)
                ->with('abort', '投稿者以外のレビューは編集できません。');
        }

        $review->update($request->validated());

        return redirect()
            ->route('books.show', $review->book_id)
            ->with('success', 'レビューを更新しました。');
    }

    public function destroy(Review $review)
    {
        if ($review->user_id !== Auth::id()) {
            return redirect()
                ->route('books.show', $review->book_id)
                ->with('abort', '投稿者以外のレビューは削除できません。');
        }

        $bookId = $review->book_id;
        $review->delete();

        return redirect()
            ->route('books.show', $bookId)
            ->with('success', 'レビューを削除しました。');
    }
}