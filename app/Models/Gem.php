<?php
namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

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
}