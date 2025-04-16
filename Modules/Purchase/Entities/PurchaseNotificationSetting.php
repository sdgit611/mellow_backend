<?php

namespace Modules\Purchase\Entities;

use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseNotificationSetting extends Model
{

    use HasCompany;

    protected $guarded = ['id'];

}
