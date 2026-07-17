<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genres = [
            ['name' => '小説'],
            ['name' => 'ビジネス'],
            ['name' => '技術書'],
            ['name' => '自己啓発'],
            ['name' => 'エッセイ'],
            ['name' => '歴史'],
            ['name' => '科学'],
            ['name' => '芸術'],
            ['name' => '料理'],
            ['name' => '旅行'],
        ];

        foreach ($genres as $genreData) {
            Genre::updateOrCreate(
                ['name' => $genreData['name']], 
                $genreData
            );        
            }
    }
}
