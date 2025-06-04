<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Notifications\CustomVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    // Role constants
    public const ROLE_CLIENT = 'client';
    public const ROLE_SELLER = 'seller';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'username',
        'email',
        'telephone',
        'profile_photo_path',
        'bio',
        'experience',
        'role',
        'password',
        'is_online',
        'last_seen_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
        'full_name',
        'is_currently_online',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_online' => 'boolean',
            'last_seen_at' => 'datetime',
        ];
    }

    // Accessors

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        if ($this->first_name && $this->last_name) {
            return "{$this->first_name} {$this->last_name}";
        }
        
        return $this->name ?? $this->username ?? 'Unknown User';
    }

    /**
     * Check if user is currently online (within last 5 minutes).
     *
     * @return bool
     */
    public function getIsCurrentlyOnlineAttribute(): bool
    {
        if ($this->is_online) {
            return true;
        }

        return $this->last_seen_at && $this->last_seen_at->gt(now()->subMinutes(5));
    }

    // Role Methods

    public function isClient(): bool
    {
        return $this->role === self::ROLE_CLIENT;
    }

    public function isSeller(): bool
    {
        return $this->role === self::ROLE_SELLER;
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    /**
     * Get the user's dashboard route based on their role.
     *
     * @return string
     */
    public function getDashboardRoute(): string
    {
        return match ($this->role) {
            self::ROLE_SELLER => route('seller.dashboard'),
            self::ROLE_CLIENT => route('client.dashboard'),
            default => route('home')
        };
    }

    /**
     * Get the user's dashboard route name based on their role.
     *
     * @return string
     */
    public function getDashboardRouteName(): string
    {
        return match ($this->role) {
            self::ROLE_SELLER => 'seller.dashboard',
            self::ROLE_CLIENT => 'client.dashboard',
            default => 'home'
        };
    }

    // Broadcasting Methods

    /**
     * Get the channels that model events should broadcast on.
     *
     * @param string $event
     * @return array
     */
    public function broadcastOn(string $event): array
    {
        return [
            new PrivateChannel('user.' . $this->id),
            new PrivateChannel('role.' . $this->role),
        ];
    }

    /**
     * Get the data that should be broadcast with model events.
     *
     * @param string $event
     * @return array
     */
    public function broadcastWith(string $event): array
    {
        return [
            'id' => $this->id,
            'name' => $this->full_name,
            'username' => $this->username,
            'role' => $this->role,
            'is_online' => $this->is_currently_online,
            'last_seen_at' => $this->last_seen_at?->toISOString(),
            'profile_photo_url' => $this->profile_photo_url,
        ];
    }

    // Online Status Methods

    /**
     * Mark user as online.
     */
    public function markAsOnline(): void
    {
        $this->update([
            'is_online' => true,
            'last_seen_at' => now(),
        ]);
    }

    /**
     * Mark user as offline.
     */
    public function markAsOffline(): void
    {
        $this->update([
            'is_online' => false,
            'last_seen_at' => now(),
        ]);
    }

    /**
     * Update last seen timestamp.
     */
    public function updateLastSeen(): void
    {
        $this->update(['last_seen_at' => now()]);
    }

    // Relationships

    /**
     * Get messages sent by this user.
     *
     * @return HasMany
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get messages received by this user.
     *
     * @return HasMany
     */
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Get conversations this user participates in.
     *
     * @return BelongsToMany
     */
    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants')
            ->withPivot(['joined_at', 'last_read_at'])
            ->withTimestamps();
    }

    // Query Scopes

    /**
     * Scope for clients.
     */
    public function scopeClients($query)
    {
        return $query->where('role', self::ROLE_CLIENT);
    }

    /**
     * Scope for sellers.
     */
    public function scopeSellers($query)
    {
        return $query->where('role', self::ROLE_SELLER);
    }

    /**
     * Scope for specific role.
     */
    public function scopeWithRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Scope for online users.
     */
    public function scopeOnline($query)
    {
        return $query->where('is_online', true)
            ->orWhere('last_seen_at', '>', now()->subMinutes(5));
    }

    /**
     * Scope for offline users.
     */
    public function scopeOffline($query)
    {
        return $query->where('is_online', false)
            ->where(function ($q) {
                $q->whereNull('last_seen_at')
                  ->orWhere('last_seen_at', '<=', now()->subMinutes(5));
            });
    }

    // Notification Routing

    /**
     * Route notifications for the broadcast channel.
     *
     * @return array
     */
    public function routeNotificationForBroadcast()
    {
        return [
            'private-user.' . $this->id,
            'private-role.' . $this->role,
        ];
    }

    /**
     * Get the notification routing information for the database driver.
     *
     * @return int
     */
    public function routeNotificationForDatabase()
    {
        return $this->id;
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail($this));
    }
}