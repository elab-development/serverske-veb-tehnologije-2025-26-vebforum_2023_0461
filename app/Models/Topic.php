<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;
    protected $fillable = [
    'title',
    'body',
    'category_id',
    'user_id'
];
    public function category()
{
    return $this->belongsTo(Category::class);
}

public function user()
{
    return $this->belongsTo(User::class);
}

public function posts()
{
    return $this->hasMany(Post::class);
}

public function votes()
{
    return $this->hasMany(Vote::class);
}
}
