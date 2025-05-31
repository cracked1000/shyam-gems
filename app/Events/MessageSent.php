<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        \Log::debug('MessageSent: Constructing with message ID ' . $message->id);
        $this->message = $message->load(['sender', 'receiver']);
    }

    public function broadcastOn()
    {
        \Log::debug('MessageSent: Broadcasting on channel messages.' . $this->message->receiver_id);
        return new Channel('messages.' . $this->message->receiver_id);
    }

    public function broadcastWith()
    {
        try {
            $data = $this->message->load(['sender', 'receiver'])->toArray();
            \Log::debug('MessageSent: Broadcasting data', ['message_id' => $this->message->id, 'data' => $data]);
            return ['message' => $data];
        } catch (\Exception $e) {
            \Log::error('MessageSent: Failed to load relationships for broadcast', [
                'error' => $e->getMessage(),
                'message_id' => $this->message->id,
            ]);
            throw $e;
        }
    }
}