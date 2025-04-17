<?php

use App\Models\Company;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Recruit\Entities\ApplicationSource;
use Modules\Recruit\Entities\RecruitJobApplication;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add the 'company_id' column and foreign key if it doesn't exist
        if (!Schema::hasColumn('application_sources', 'is_predefined')) {
            Schema::table('application_sources', function (Blueprint $table) {
                $table->boolean('is_predefined')->default(true);
                $table->integer('company_id')->unsigned()->after('id')->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade')->onUpdate('cascade');
            });
        }

        // Update application sources with company data
        $companies = Company::all();

        foreach ($companies as $key => $company) {
            if ($key == 0) {
                // Update the company_id for existing application sources where it's null
                ApplicationSource::whereNull('company_id')->update(['company_id' => $company->id]);
            } else {
                // Insert new application sources for the company
                $sourceList = [
                    ['application_source' => 'LinkedIn', 'company_id' => $company->id, 'is_predefined' => true],
                    ['application_source' => 'Facebook', 'company_id' => $company->id, 'is_predefined' => true],
                    ['application_source' => 'Instagram', 'company_id' => $company->id, 'is_predefined' => true],
                    ['application_source' => 'Twitter', 'company_id' => $company->id, 'is_predefined' => true],
                    ['application_source' => 'Other', 'company_id' => $company->id, 'is_predefined' => true],
                ];

                ApplicationSource::insertOrIgnore($sourceList);
            }
        }

        // Update RecruitJobApplication with the correct application_source_id
        $applications = RecruitJobApplication::all();

        foreach ($applications as $jobApplication) {
            $source = $jobApplication->source;

            if ($source) {
                $applicationSource = ApplicationSource::where('company_id', $jobApplication->company_id)
                                                      ->where('application_source', $source->application_source)
                                                      ->first();

                if ($applicationSource) {
                    $jobApplication->application_source_id = $applicationSource->id;
                    $jobApplication->save();
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('application_sources', function (Blueprint $table) {
            // Drop foreign key constraint before dropping the column
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
