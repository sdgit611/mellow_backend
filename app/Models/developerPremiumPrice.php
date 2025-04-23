<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class developerPremiumPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status' => 'boolean',
    ];

    //  Classic accessor
    public function getPriceAttribute($value)
    {
        return (float) $value;
    }

 
    //  Classic mutator
    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = (float) $value;
    }
}
