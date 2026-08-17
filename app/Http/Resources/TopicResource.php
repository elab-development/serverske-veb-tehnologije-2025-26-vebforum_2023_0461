<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TopicResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'category_id' => $this->category_id,
            'user_id' => $this->user_id,
            'vote_score' => (int) ($this->votes_sum_value ?? $this->votes()->sum('value')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}