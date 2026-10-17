<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token'
        ];

    
    protected $hidden = [
        'password',
        'remember_token',
    ];

    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    
    

    public function books(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Book::class);
    }

    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Review::class);
    }

    
    public function favoriteBooks(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'favorites')->withTimestamps();
    }

    
    public function likedReviews(): BelongsToMany
    {
        return $this->belongsToMany(Review::class, 'review_likes')->withTimestamps();
    }
} 
