<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Post extends Model
{
    protected $fillable = ['body', 'topic_id', 'user_id'];
    use HasFactory;
    public function topic()
{
    return $this->belongsTo(Topic::class);
}

public function user()
{
    return $this->belongsTo(User::class); 
}

public function comments()
{
    return $this->hasMany(Comment::class);
}

public function likes()
{
    return $this->hasMany(Like::class);
}
}
