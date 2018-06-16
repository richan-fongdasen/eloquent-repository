<?php

namespace RichanFongdasen\Repository\Tests\Supports\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{   
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'post_category_id',
        'user_id',
        'title',
        'content',
        'published',
    ];

    /**
     * The number of items to be shown per page.
     *
     * @var integer
     */
    protected $perPage = 2;

    /**
     * Has many relationship with the Comment model.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Belongs to relationship with the PostCategory model.
     */
    public function postCategory()
    {
        return $this->belongsTo(PostCategory::class);
    }

    /**
     * Belongs to relationship with the User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
