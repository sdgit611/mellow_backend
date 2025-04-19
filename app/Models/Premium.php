<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Premium extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

     //  Classic accessor
     public function getNameAttribute($value)
     {
         return ucfirst($value);
     }
 
     //  Classic mutator
     public function setNameAttribute($value)
     {
         $this->attributes['name'] = ucfirst($value);
     }
}