<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewQuestion extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'Question', 'star'];
}
