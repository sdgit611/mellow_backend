<?php

namespace Modules\RestAPI\Http\Controllers;

use App\Models\Company;
use Froiden\RestAPI\ApiResponse;
use Illuminate\Routing\Controller;

class AppController extends Controller
{
    public function app()
    {
        $setting = Company::first();
        $setting->makeHidden(
            'weather_key',
            'currency_converter_key',
            'google_recaptcha_key',
            'google_recaptcha_secret',
            'show_review_modal',
            'show_public_message',
            'supported_until',
            'currency_id',
            'system_update',
            'purchase_code',
            'google_recaptcha',
            'hide_cron_message',
            'company_phone',
            'rounded_theme,google_map_key,'
        );

        return ApiResponse::make('Application data fetched successfully', $setting->toArray());
    }
}
