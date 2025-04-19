<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('employee_details', function (Blueprint $table) {
            $table->integer('developer_id')->nullable();
            $table->string('job')->nullable();
            $table->integer('total_hours')->nullable();
            $table->integer('rating')->nullable();
            $table->string('skills')->nullable();
            $table->string('completed_job')->nullable();
            $table->string('resume')->nullable();
            $table->string('adharcard')->nullable();
            $table->string('pancard')->nullable(); // only once
        });

        Schema::create('education', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_details_id')->unsigned()->nullable();
            $table->foreign('employee_details_id')->references('id')->on('employee_details')->onDelete('cascade')->onUpdate('cascade');
            $table->string('collage_name')->nullable();
            $table->string('degree')->nullable();
            $table->string('percentage')->nullable();
            $table->string('passing_year')->nullable();
            $table->timestamps();
        });

        Schema::create('bank_details', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_details_id')->unsigned()->nullable();
            $table->foreign('employee_details_id')->references('id')->on('employee_details')->onDelete('cascade')->onUpdate('cascade');
            $table->string('branch_name')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifc_code')->nullable();
            $table->string('micr_number')->nullable();
            $table->string('passbook')->nullable();
            $table->string('account_type')->nullable(); // changed to snake_case
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */

    public function down()
    {
        Schema::table('employee_details', function (Blueprint $table) {
            $table->dropColumn([
                'developer_id', 'job', 'total_hours', 'rating', 'skills',
                'completed_job', 'resume', 'adharcard', 'pancard'
            ]);
        });

        Schema::dropIfExists('education');
        Schema::dropIfExists('bank_details');
    }

};
