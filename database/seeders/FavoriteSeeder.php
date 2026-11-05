<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Book;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
        $users = User::all();
        $books = Book::all();

   
        if ($users->isNotEmpty() && $books->isNotEmpty()) {

        
            foreach ($users as $user) {
                
              
                $favoriteCount = rand(3, 5);

             
                $randomBooks = $books->random($favoriteCount);

                $bookIds = $randomBooks->pluck('id')->toArray();

           
                $user->favoriteBooks()->syncWithoutDetaching($bookIds);
            }
        }
    }
}