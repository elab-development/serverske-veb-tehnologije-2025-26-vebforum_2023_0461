<?php

namespace App\Policies;

use App\Models\Topic;
use App\Models\User;

class TopicPolicy
{
    public function update(User $user, Topic $topic): bool
    {
        return $this->moderatesOrOwns($user, $topic);
    }

    public function delete(User $user, Topic $topic): bool
    {
        return $this->moderatesOrOwns($user, $topic);
    }

    private function moderatesOrOwns(User $user, Topic $topic): bool
    {
        return in_array($user->role, ['admin', 'moderator'], true) || $topic->user_id === $user->id;
    }
}
