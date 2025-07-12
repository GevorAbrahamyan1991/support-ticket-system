<?php

namespace App\Events;

use App\Models\Comment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentAdded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $comment;
    public $ticketId;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment->load('user');
        $this->ticketId = $comment->ticket_id;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('ticket.' . $this->ticketId);
    }

    public function broadcastWith()
    {
        return [
            'comment' => [
                'id' => $this->comment->id,
                'content' => $this->comment->content,
                'user' => [
                    'id' => $this->comment->user->id,
                    'name' => $this->comment->user->name,
                ],
                'created_at' => $this->comment->created_at->toDateTimeString(),
            ]
        ];
    }
} 