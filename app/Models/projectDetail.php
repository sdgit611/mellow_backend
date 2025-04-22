<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class projectDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_details_id',
        'pro_id',
        'title',
        'link',
        'github_link',
        'description',
        'screenshot_image',
    ];
}
