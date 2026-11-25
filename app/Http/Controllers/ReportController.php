<?php

namespace App\Http\Controllers;

use App\Enums\ReadingPlanStatus;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $reviews = $user->reviews()->with('book.genres')->get();

        $totalReviews = $reviews->count();
        $booksRead = $user->readingPlans()
            ->where('status', ReadingPlanStatus::Completed)
            ->count();
        $avgRating = $totalReviews > 0 ? (float) $reviews->avg('rating') : 0;

        $ratingDistribution = collect([1, 2, 3, 4, 5])->mapWithKeys(function ($star, $index) use ($reviews) {
            return [$index => $reviews->where('rating', $star)->count()];
        });

        $topRatedBooks = $reviews
            ->where('rating', '>=', 4)
            ->sortByDesc('rating')
            ->take(5)
            ->map(function ($review) {
                return [
                    'id' => $review->book->id,
                    'title' => $review->book->title,
                    'author' => $review->book->author,
                    'rating' => $review->rating,
                ];
            })
            ->values();

        $genreRatings = $reviews
            ->flatMap(function ($review) {
                return $review->book->genres->map(function ($genre) use ($review) {
                    return [
                        'id' => $genre->id,
                        'name' => $genre->name,
                        'rating' => $review->rating,
                    ];
                });
            })
            ->groupBy('id')
            ->map(function ($items) {
                $first = $items->first();
                return [
                    'id' => $first['id'],
                    'name' => $first['name'],
                    'count' => $items->count(),
                    'average_rating' => round($items->avg('rating'), 1),
                ];
            })
            ->sortByDesc('average_rating')
            ->take(5)
            ->values();

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