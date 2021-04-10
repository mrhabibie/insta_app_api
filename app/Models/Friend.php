<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function followingPosts()
    {
        return $this->hasMany(Post::class, 'user_id', 'to_id');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'ACCEPT');
    }
}
