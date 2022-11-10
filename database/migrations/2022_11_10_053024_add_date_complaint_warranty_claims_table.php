<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateComplaintWarrantyClaimsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warranty_claims', function (Blueprint $table) {
            $table->date('date_complaint_closed')->after('date_complaint');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warranty_claims', function (Blueprint $table) {
            $table->dropColumn('date_complaint_closed');
        });
    }
}
