<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewLikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $users = User::all();
        $reviews = Review::all();

        if ($users->isNotEmpty() && $reviews->isNotEmpty()) {

           
            foreach ($reviews as $review) {

                
                $authorId = $review->user_id;

                
                $otherUsers = $users->reject(function ($user) use ($authorId) {
                    return $user->id === $authorId;
                });

                
                $likeCount = rand(0, 3);

               
                $randomUsersCount = min($likeCount, $otherUsers->count());
                
                if ($randomUsersCount > 0) {
                    $likedUsers = $otherUsers->random($randomUsersCount);

                    $userIds = $likedUsers->pluck('id')->toArray();

                    
                    foreach ($likedUsers as $user) {
                        $user->likedReviews()->syncWithoutDetaching([$review->id]);
                    }
                }
            }
        }
    }
}