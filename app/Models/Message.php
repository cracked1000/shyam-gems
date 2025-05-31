<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'content',
        'is_read',
        'read_at',
        'message_type',
        'metadata',
        'is_deleted_by_sender',
        'is_deleted_by_receiver',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'metadata' => 'array',
        'is_deleted_by_sender' => 'boolean',
        'is_deleted_by_receiver' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = [
        'read_at',
        'deleted_at',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Update conversation last activity when message is created
        static::created(function ($message) {
            $message->updateConversationActivity();
            $message->clearUserConversationCache();
        });

        // Clear cache when message is updated
        static::updated(function ($message) {
            $message->clearUserConversationCache();
        });

        // Clear cache when message is deleted
        static::deleted(function ($message) {
            $message->clearUserConversationCache();
        });
    }

    /**
     * Get the sender of the message
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the receiver of the message
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Get the attachments for the message
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(MessageAttachment::class);
    }

    /**
     * Scope to get messages between two users
     */
    public function scopeBetweenUsers($query, $user1Id, $user2Id)
    {
        return $query->where(function ($q) use ($user1Id, $user2Id) {
            $q->where('sender_id', $user1Id)->where('receiver_id', $user2Id);
        })->orWhere(function ($q) use ($user1Id, $user2Id) {
            $q->where('sender_id', $user2Id)->where('receiver_id', $user1Id);
        });
    }

    /**
     * Scope to get unread messages for a user
     */
    public function scopeUnreadForUser($query, $userId)
    {
        return $query->where('receiver_id', $userId)
                    ->where('is_read', false);
    }

    /**
     * Scope to get messages not deleted by user
     */
    public function scopeNotDeletedByUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('sender_id', $userId)->where('is_deleted_by_sender', false);
        })->orWhere(function ($q) use ($userId) {
            $q->where('receiver_id', $userId)->where('is_deleted_by_receiver', false);
        });
    }

    /**
     * Mark message as read
     */
    public function markAsRead(): bool
    {
        if (!$this->is_read) {
            return $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
        
        return true;
    }

    /**
     * Mark message as deleted by user
     */
    public function deleteForUser($userId): bool
    {
        if ($this->sender_id == $userId) {
            $this->is_deleted_by_sender = true;
        } elseif ($this->receiver_id == $userId) {
            $this->is_deleted_by_receiver = true;
        } else {
            return false;
        }

        // If both users have deleted the message, soft delete it
        if ($this->is_deleted_by_sender && $this->is_deleted_by_receiver) {
            $this->delete();
        } else {
            $this->save();
        }

        return true;
    }

    /**
     * Check if message is deleted for user
     */
    public function isDeletedForUser($userId): bool
    {
        if ($this->sender_id == $userId) {
            return $this->is_deleted_by_sender;
        } elseif ($this->receiver_id == $userId) {
            return $this->is_deleted_by_receiver;
        }

        return false;
    }

    /**
     * Get formatted message content
     */
    public function getFormattedContentAttribute(): string
    {
        // Basic text formatting - you can extend this
        $content = $this->content;
        
        // Convert URLs to links
        $content = preg_replace(
            '/(https?:\/\/[^\s]+)/',
            '<a href="$1" target="_blank" class="text-blue-500 hover:underline">$1</a>',
            $content
        );

        // Convert line breaks to HTML
        $content = nl2br($content);

        return $content;
    }

    /**
     * Get message preview (truncated content)
     */
    public function getPreviewAttribute(): string
    {
        return \Str::limit($this->content, 50);
    }

    /**
     * Update conversation last activity
     */
    protected function updateConversationActivity(): void
    {
        $conversation = Conversation::firstOrCreate([
            'user1_id' => min($this->sender_id, $this->receiver_id),
            'user2_id' => max($this->sender_id, $this->receiver_id),
        ]);

        $conversation->update([
            'last_message_id' => $this->id,
            'last_activity_at' => $this->created_at,
        ]);
    }

    /**
     * Clear user conversation cache
     */
    protected function clearUserConversationCache(): void
    {
        Cache::forget('user_conversations_' . $this->sender_id);
        Cache::forget('user_conversations_' . $this->receiver_id);
        Cache::forget('recent_conversation_' . $this->sender_id . '_' . $this->receiver_id);
        Cache::forget('recent_conversation_' . $this->receiver_id . '_' . $this->sender_id);
    }

    /**
     * Get message statistics for user
     */
    public static function getStatsForUser($userId): array
    {
        $cacheKey = 'message_stats_' . $userId;
        
        return Cache::remember($cacheKey, 300, function () use ($userId) {
            return [
                'total_sent' => static::where('sender_id', $userId)->count(),
                'total_received' => static::where('receiver_id', $userId)->count(),
                'unread_count' => static::unreadForUser($userId)->count(),
                'conversations_count' => Conversation::forUser($userId)->count(),
            ];
        });
    }

    /**
     * Search messages for user
     */
    public static function searchForUser($userId, $query, $limit = 20)
    {
        return static::where(function ($q) use ($userId) {
            $q->where('sender_id', $userId)->orWhere('receiver_id', $userId);
        })
        ->where('content', 'like', '%' . $query . '%')
        ->notDeletedByUser($userId)
        ->with(['sender', 'receiver'])
        ->orderBy('created_at', 'desc')
        ->limit($limit)
        ->get();
    }

    /**
     * Get recent messages for user
     */
    public static function getRecentForUser($userId, $limit = 50)
    {
        $cacheKey = 'recent_messages_' . $userId;
        
        return Cache::remember($cacheKey, 300, function () use ($userId, $limit) {
            return static::where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)->orWhere('receiver_id', $userId);
            })
            ->notDeletedByUser($userId)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
        });
    }
}