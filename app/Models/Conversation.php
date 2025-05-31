<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user1_id',
        'user2_id',
        'last_message_id',
        'last_activity_at',
        'is_archived_by_user1',
        'is_archived_by_user2',
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
        'is_archived_by_user1' => 'boolean',
        'is_archived_by_user2' => 'boolean',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Clear cache when conversation is updated
        static::updated(function ($conversation) {
            $conversation->clearCache();
        });

        // Clear cache when conversation is deleted
        static::deleted(function ($conversation) {
            $conversation->clearCache();
        });
    }

    /**
     * Get the first user in the conversation
     */
    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    /**
     * Get the second user in the conversation
     */
    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    /**
     * Get the last message in the conversation
     */
    public function lastMessage(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }

    /**
     * Get all messages in the conversation
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)
                    ->where(function ($query) {
                        $query->where(function ($q) {
                            $q->where('sender_id', $this->user1_id)
                              ->where('receiver_id', $this->user2_id);
                        })->orWhere(function ($q) {
                            $q->where('sender_id', $this->user2_id)
                              ->where('receiver_id', $this->user1_id);
                        });
                    });
    }

    /**
     * Scope to get conversations for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user1_id', $userId)
                    ->orWhere('user2_id', $userId);
    }

    /**
     * Scope to get active (non-archived) conversations for a user
     */
    public function scopeActiveForUser($query, $userId)
    {
        return $query->forUser($userId)
                    ->where(function ($q) use ($userId) {
                        $q->where(function ($subQ) use ($userId) {
                            $subQ->where('user1_id', $userId)
                                 ->where('is_archived_by_user1', false);
                        })->orWhere(function ($subQ) use ($userId) {
                            $subQ->where('user2_id', $userId)
                                 ->where('is_archived_by_user2', false);
                        });
                    });
    }

    /**
     * Get the other user in the conversation
     */
    public function getOtherUser($userId): ?User
    {
        if ($this->user1_id == $userId) {
            return $this->user2;
        } elseif ($this->user2_id == $userId) {
            return $this->user1;
        }
        
        return null;
    }

    /**
     * Get the other user ID in the conversation
     */
    public function getOtherUserId($userId): ?int
    {
        if ($this->user1_id == $userId) {
            return $this->user2_id;
        } elseif ($this->user2_id == $userId) {
            return $this->user1_id;
        }
        
        return null;
    }

    /**
     * Check if conversation is archived for user
     */
    public function isArchivedForUser($userId): bool
    {
        if ($this->user1_id == $userId) {
            return $this->is_archived_by_user1;
        } elseif ($this->user2_id == $userId) {
            return $this->is_archived_by_user2;
        }
        
        return false;
    }

    /**
     * Archive conversation for user
     */
    public function archiveForUser($userId): bool
    {
        if ($this->user1_id == $userId) {
            $this->is_archived_by_user1 = true;
        } elseif ($this->user2_id == $userId) {
            $this->is_archived_by_user2 = true;
        } else {
            return false;
        }

        return $this->save();
    }

    /**
     * Unarchive conversation for user
     */
    public function unarchiveForUser($userId): bool
    {
        if ($this->user1_id == $userId) {
            $this->is_archived_by_user1 = false;
        } elseif ($this->user2_id == $userId) {
            $this->is_archived_by_user2 = false;
        } else {
            return false;
        }

        return $this->save();
    }

    /**
     * Get unread message count for user
     */
    public function unreadCountForUser($userId): int
    {
        return Message::where('receiver_id', $userId)
                      ->whereIn('sender_id', [$this->user1_id, $this->user2_id])
                      ->where('receiver_id', '!=', $this->getOtherUserId($userId))
                      ->where('is_read', false)
                      ->count();
    }

    /**
     * Clear conversation cache
     */
    protected function clearCache(): void
    {
        Cache::forget('user_conversations_' . $this->user1_id);
        Cache::forget('user_conversations_' . $this->user2_id);
        Cache::forget('recent_conversation_' . $this->user1_id . '_' . $this->user2_id);
        Cache::forget('recent_conversation_' . $this->user2_id . '_' . $this->user1_id);
    }

    /**
     * Get conversations for user with additional data
     */
    public static function getForUser($userId, $limit = 20)
    {
        $cacheKey = 'user_conversations_' . $userId;

        return Cache::remember($cacheKey, now()->addMinutes(5), function () use ($userId, $limit) {
            $conversations = static::activeForUser($userId)
                ->with(['user1', 'user2', 'lastMessage'])
                ->orderBy('last_activity_at', 'desc')
                ->take($limit)
                ->get();

            return $conversations->map(function ($conversation) use ($userId) {
                $otherUser = $conversation->getOtherUser($userId);
                $conversation->other_user = $otherUser;
                $conversation->unread_count = $conversation->unreadCountForUser($userId);
                $conversation->last_message_preview = $conversation->lastMessage ? $conversation->lastMessage->preview : null;
                return $conversation;
            });
        });
    }
}