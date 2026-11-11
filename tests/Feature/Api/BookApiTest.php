<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase; 

    public function test_can_get_books_list(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create(['name' => '技術書']);
        
        $book = Book::factory()->for($user)->create();
        $book->genres()->attach($genre);

        $response = $this->getJson('/api/v1/books');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'title', 'author', 'isbn', 'published_date', 'genres']
                     ]
                 ]);
    }

    public function test_can_update_book_via_api(): void
    {
        $user = User::factory()->create();
        $genre1 = Genre::factory()->create();
        $genre2 = Genre::factory()->create();
        
      
        $book = Book::factory()->for($user)->create();
        $book->genres()->attach($genre1);

        $payload = [
            'title' => '更新後のタイトル',
            'author' => '更新後の著者',
            'isbn' => '9784123456789',
            'published_date' => '2026-11-06',
            'description' => '更新テストの説明文',
            'genres' => [$genre2->id],
        ];

      
        $response = $this->actingAs($user)
                         ->putJson("/api/v1/books/{$book->id}", $payload);

        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => '更新後のタイトル']);

        
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => '更新後のタイトル',
        ]);
    }

    public function test_can_delete_book_via_api(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();

        
        $response = $this->actingAs($user)
                         ->deleteJson("/api/v1/books/{$book->id}");

        
        $response->assertNoContent(); 
        
        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
    }
}
