<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_details', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_details_id')->unsigned()->nullable();
            $table->foreign('employee_details_id')->references('id')->on('employee_details')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('pro_id')->nullable();
            $table->string('title')->nullable();
            $table->string('link')->nullable();
            $table->string('github_link')->nullable();
            $table->longText('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_details');
    }
};
