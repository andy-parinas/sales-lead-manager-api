<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarrantyClaimsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warranty_claims', function (Blueprint $table) {
            $table->id();
            $table->date('date_complaint');
            $table->string('complaint_received')->nullable();
            $table->string('complaint_type')->nullable();
            $table->string('home_addition_type')->nullable();
            $table->text('complaint_description')->nullable();
            $table->string('contacted_franchise')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('lead_id');
            $table->timestamps();

            $table->foreign('lead_id')->references('id')->on('leads')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warranty_claims');
    }
}
