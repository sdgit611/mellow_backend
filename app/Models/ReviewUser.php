<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\ReviewQuestion;

class ReviewUser extends Model
{
    use HasFactory;

    protected $fillable = ['company_id','user_id', 'task_id','review_id','star'];

    public function user() 
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function question()
    {
        return $this->belongsTo(ReviewQuestion::class, 'review_questions_id');
    }
}
