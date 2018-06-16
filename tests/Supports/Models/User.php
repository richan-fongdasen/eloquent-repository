<?php

namespace RichanFongdasen\Repository\Tests\Supports\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Has many relationship with the Comment model.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Has many relationship with the Post model.
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}