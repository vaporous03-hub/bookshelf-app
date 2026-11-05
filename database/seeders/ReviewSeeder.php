<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use App\Models\Book;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        //Review::truncate();

        $faker = Faker::create('ja_JP');

        
        $users = User::all();
        $books = Book::all();

        
        $comments = [
            5 => ['最高の本です！', '素晴らしい一冊でした！', '感動しました！'],
            4 => ['とても面白かったです。', '満足度の高い本です。', '一読の価値ありです。'],
            3 => ['可もなく不可もなくといった内容でした。', '普通の小説でした。', '少し期待しすぎました。'],
        ];

        
        if ($users->isNotEmpty() && $books->isNotEmpty()) {

           
            foreach ($books as $book) {

                
                $shuffledUsers = $users->shuffle();

               
                $reviewCount = rand(2, 4);

                for ($i = 0; $i < $reviewCount; $i++) {
                    
                    $user = $shuffledUsers[$i];

                  
                    $rating = rand(3, 5);

                   
                    $prefix = $comments[$rating][array_rand($comments[$rating])];
                    $comment = $prefix . $faker->realText(100);

                   
                    Review::create([
                        'user_id' => $user->id,         
                        'book_id' => $book->id,         
                        'rating' => $rating,
                        'comment' => $comment,
                    ]);
                }
            }
        }
    }
}