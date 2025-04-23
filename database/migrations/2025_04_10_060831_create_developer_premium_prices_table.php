<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeveloperPremiumPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('developer_premium_prices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('price', 8, 2)->nullable();
            $table->tinyInteger('status')->default(1)->comment("1 = Active, 0 = Inactive"); // 0 = pending, 1 = paid, 2 = failed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('developer_premium_prices');
    }
}
