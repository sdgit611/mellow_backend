<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeveloperPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('developer_payments', function (Blueprint $table) {
            $table->id();
            $table->integer('developer_id');
            $table->string('payment_id');
            $table->string('order_id')->nullable();
            $table->string('signature')->nullable();
            $table->unsignedBigInteger('developer_premium_prices_id');
            $table->foreign('developer_premium_prices_id')->references('id')->on('developer_premium_prices')->onDelete('cascade'); 
            $table->date('expired')->nullable();
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
        Schema::dropIfExists('developer_payments');
    }
}
