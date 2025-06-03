<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class Gem extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'gems';

    protected $fillable = [
        'name',
        'description',
        'image',
        'seller_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Ensure seller_id is always stored as string for consistency
    public function setSellerIdAttribute($value)
    {
        $this->attributes['seller_id'] = (string) $value;
    }

    // Relationship with User
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    // Accessor for image URL
    public function getImageUrlAttribute()
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }
        return null;
    }

    // Check if image exists
    public function hasImage()
    {
        return $this->image && Storage::disk('public')->exists($this->image);
    }

    // Simplified boot method for automatic image deletion and debug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($gem) {
            Log::info('Creating gem with seller_id: ' . $gem->seller_id, [
                'name' => $gem->name,
                'description' => $gem->description,
                'image' => $gem->image,
                'seller_id' => $gem->seller_id,
            ]);
        });

        static::deleting(function ($gem) {
            if ($gem->image && Storage::disk('public')->exists($gem->image)) {
                try {
                    Storage::disk('public')->delete($gem->image);
                    Log::info('Auto-deleted image: ' . $gem->image);
                } catch (\Exception $e) {
                    Log::error('Failed to auto-delete image: ' . $e->getMessage());
                }
            }
        });
    }

    // Scope for seller's gems with string casting
    public function scopeForSeller($query, $sellerId)
    {
        return $query->where('seller_id', (string) $sellerId);
    }

    // Simplified delete method
    public function safeDelete()
    {
        try {
            return $this->delete();
        } catch (\Exception $e) {
            Log::error('Safe delete error: ' . $e->getMessage());
            return false;
        }
    }
}