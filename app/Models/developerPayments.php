<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class developerPayments extends Model
{
    use HasFactory;

    protected $fillable = [
        'developer_id',
        'payment_id',
        'order_id',
        'signature',
        'developer_premium_prices_id',
        'expired',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'expired' => 'date',
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
