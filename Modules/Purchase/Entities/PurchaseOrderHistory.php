<?php

namespace Modules\Purchase\Entities;

use App\Models\User;
use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderHistory extends Model
{

    use HasFactory, HasCompany;

    protected $fillable = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
