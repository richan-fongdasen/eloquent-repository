<?php

namespace RichanFongdasen\Repository\Tests\Supports\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'post_id',
        'user_id',
        'content',
    ];

    /**
     * Belongs to relationship with the Post model.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Belongs to relationship with the User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
