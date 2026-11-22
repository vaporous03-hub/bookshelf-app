<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $reviews = $user->reviews()->with('book.genres')->get();
        
        $totalReviews = $reviews->count();
        $booksRead = $user->books()->count(); 
        $avgRating = $totalReviews > 0 ? $reviews->avg('rating') : 0;

        $ratingDistribution = collect([1, 2, 3, 4, 5])->mapWithKeys(function ($star) use ($reviews) {
            return [$star - 1 => $reviews->where('rating', $star)->count()];
        });

        $topRatedBooks = $reviews->sortByDesc('rating')->take(5)->map(function ($review) {
            return [
                'id' => $review->book->id,
                'title' => $review->book->title,
                'author' => $review->book->author,
                'rating' => $review->rating,
            ];
        })->values();

        $genreRatings = collect();

        $stats = [
            'summary' => [
                'total_reviews' => $totalReviews,
                'books_read' => $booksRead,
                'average_rating' => $avgRating,
            ],
            'rating_distribution' => $ratingDistribution,
            'top_rated_books' => $topRatedBooks,
            'genre_ratings' => $genreRatings,
        ];

        return view('reports.index', compact('stats'));
    }
}