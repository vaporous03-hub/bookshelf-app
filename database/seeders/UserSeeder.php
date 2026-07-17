<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => '山田太郎',
            'email' => 'yamada@example.com',
            'password' => bcrypt('password'),
        ]);
        User::create([
            'name' => '鈴木花子',
            'email' => 'suzuki@example.com',
            'password' => bcrypt('password'),
        ]);
        User::create([
            'name' => '田中一郎',
            'email' => 'tanaka@example.com',
            'password' => bcrypt('password'),
        ]);
        User::create([
            'name' => '佐藤美咲',
            'email' => 'sato@example.com',
            'password' => bcrypt('password'),
        ]);
        User::create([
            'name' => '高橋健太',
            'email' => 'takahashi@example.com',
            'password' => bcrypt('password'),
        ]);
     

    }
}