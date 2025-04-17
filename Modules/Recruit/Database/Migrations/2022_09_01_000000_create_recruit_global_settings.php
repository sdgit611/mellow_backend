<?php

use App\Scopes\CompanyScope;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Recruit\Entities\ApplicationSource;
use Modules\Recruit\Entities\RecruitGlobalSetting;
use Modules\Recruit\Entities\RecruitSetting;

return new class extends Migration
{
    public function up()
    {
        // Create the recruit_global_settings table
        Schema::create('recruit_global_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('purchase_code')->nullable();
            $table->string('license_type', 20)->nullable();
            $table->timestamp('supported_until')->nullable();
            $table->timestamps();
        });

        // Copy purchase_code from RecruitSetting to RecruitGlobalSetting
        $setting = RecruitSetting::withoutGlobalScope(CompanyScope::class)->first();
        $newSetting = new RecruitGlobalSetting;

        if ($setting) {
            $newSetting->purchase_code = $setting->purchase_code;
        }
        $newSetting->saveQuietly();

        // Drop the purchase_code column from recruit_settings table
        Schema::table('recruit_settings', function (Blueprint $table) {
            $table->dropColumn(['purchase_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the recruit_global_settings table and add the purchase_code column back to recruit_settings
        Schema::table('recruit_settings', function (Blueprint $table) {
            $table->string('purchase_code')->nullable();
        });

        Schema::dropIfExists('recruit_global_settings');
    }
};
