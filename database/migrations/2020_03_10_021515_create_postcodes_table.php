<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostcodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('postcodes', function (Blueprint $table) {
            $table->id();
            $table->integer('pcode');
            $table->string('locality');
            $table->string('state');
            $table->string('delivery_office')->nullable();
            $table->string('presort_indicator')->nullable();
            $table->string('parcel_zone')->nullable();
            $table->string('bsp_number')->nullable();
            $table->string('bsp_name')->nullable();
            $table->string('category')->nullable();
            $table->string('comments')->nullable();
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
        Schema::dropIfExists('postcodes');
    }
}
