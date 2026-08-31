<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function update(User $user, Post $post): bool
    {
        return $this->moderatesOrOwns($user, $post);
    }

    public function delete(User $user, Post $post): bool
    {
        return $this->moderatesOrOwns($user, $post);
    }

    private function moderatesOrOwns(User $user, Post $post): bool
    {
        return in_array($user->role, ['admin', 'moderator'], true) || $post->user_id === $user->id;
    }
}
