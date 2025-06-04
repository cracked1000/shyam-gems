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

    /**
     * Get the primary key for the model.
     */
    public function getKeyName()
    {
        return '_id';
    }

    /**
     * Get the value of the model's primary key.
     */
    public function getKey()
    {
        return $this->getAttribute($this->getKeyName());
    }

    /**
     * Set the keys for a save update query.
     */
    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        if (isset($this->$keys)) {
            return $query->where($keys, '=', $this->getKeyForSaveQuery($keys));
        } else {
            throw new Exception('No primary key defined on model.');
        }
        return $query;
    }

    /**
     * Get the primary key value for a save query.
     */
    protected function getKeyForSaveQuery($keyName = null)
    {
        $keyName = $keyName ?: $this->getKeyName();
        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }
        return $this->getAttribute($keyName);
    }

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

    // Boot method with better error handling
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
            Log::info('Deleting gem: ' . $gem->name, [
                'id' => $gem->id ?? $gem->_id,
                'seller_id' => $gem->seller_id
            ]);

            // Delete associated image
            if ($gem->image && Storage::disk('public')->exists($gem->image)) {
                try {
                    Storage::disk('public')->delete($gem->image);
                    Log::info('Auto-deleted image: ' . $gem->image);
                } catch (\Exception $e) {
                    Log::error('Failed to auto-delete image: ' . $e->getMessage());
                }
            }
        });

        static::deleted(function ($gem) {
            Log::info('Gem successfully deleted: ' . $gem->name);
        });
    }

    // Scope for seller's gems with string casting
    public function scopeForSeller($query, $sellerId)
    {
        return $query->where('seller_id', (string) $sellerId);
    }

    // Robust delete method with multiple approaches
    public function safeDelete()
    {
        try {
            Log::info('Attempting safe delete for gem: ' . $this->name);

            // Approach 1: Standard delete
            if ($this->delete()) {
                Log::info('Gem deleted using standard delete method');
                return true;
            }

            // Approach 2: Force delete if available
            if (method_exists($this, 'forceDelete')) {
                if ($this->forceDelete()) {
                    Log::info('Gem deleted using forceDelete method');
                    return true;
                }
            }

            // Approach 3: Raw deletion
            $result = $this->getConnection()->collection($this->getTable())->deleteOne([
                '_id' => $this->getKey(),
            ]);

            if ($result->getDeletedCount() > 0) {
                Log::info('Gem deleted using raw MongoDB delete');
                return true;
            }

            Log::error('All safe delete approaches failed');
            return false;

        } catch (\Exception $e) {
            Log::error('Safe delete error: ' . $e->getMessage());
            return false;
        }
    }

    // Static method for robust deletion by ID
    public static function safeDeleteById($id, $sellerId = null)
    {
        try {
            $query = static::query();
            
            // Add seller constraint if provided
            if ($sellerId) {
                $query->where('seller_id', (string) $sellerId);
            }

            // Try multiple ID formats
            $gem = null;
            
            // Approach 1: String ID
            $gem = $query->where('_id', (string) $id)->first();
            
            // Approach 2: ObjectId if available
            if (!$gem && class_exists('MongoDB\BSON\ObjectId')) {
                try {
                    $objectId = new \MongoDB\BSON\ObjectId($id);
                    $gem = $query->where('_id', $objectId)->first();
                } catch (\Exception $e) {
                    Log::debug('ObjectId creation failed: ' . $e->getMessage());
                }
            }

            // Approach 3: Direct find
            if (!$gem) {
                $gem = static::find($id);
                if ($gem && $sellerId && $gem->seller_id !== (string) $sellerId) {
                    $gem = null; // Not authorized
                }
            }

            if (!$gem) {
                Log::warning('Gem not found for deletion', ['id' => $id, 'seller_id' => $sellerId]);
                return false;
            }

            return $gem->safeDelete();

        } catch (\Exception $e) {
            Log::error('Error in safeDeleteById: ' . $e->getMessage());
            return false;
        }
    }

    // Override the default delete method to add logging
    public function delete()
    {
        Log::info('Delete method called for gem: ' . ($this->name ?? 'Unknown'));
        
        try {
            $result = parent::delete();
            Log::info('Parent delete result: ' . ($result ? 'success' : 'failed'));
            return $result;
        } catch (\Exception $e) {
            Log::error('Error in delete method: ' . $e->getMessage());
            throw $e;
        }
    }

    // Debug method to check gem structure
    public function debugInfo()
    {
        return [
            'id' => $this->id,
            '_id' => $this->_id ?? null,
            'key' => $this->getKey(),
            'key_name' => $this->getKeyName(),
            'name' => $this->name,
            'seller_id' => $this->seller_id,
            'seller_id_type' => gettype($this->seller_id),
            'exists' => $this->exists,
            'attributes' => $this->attributes,
            'original' => $this->original,
        ];
    }
}