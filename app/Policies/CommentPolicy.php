<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function update(User $user, Comment $comment): bool
    {
        return $this->moderatesOrOwns($user, $comment);
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $this->moderatesOrOwns($user, $comment);
    }

    private function moderatesOrOwns(User $user, Comment $comment): bool
    {
        return in_array($user->role, ['admin', 'moderator'], true) || $comment->user_id === $user->id;
    }
}
