<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user,
            'employee' => $this->employee,
            'title' => $this->title,
            'content' => $this->content,
            'status' => $this->status,
            'priority' => $this->priority,
        ];
    }
}
