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
        Schema::table('incidents', function (Blueprint $table) {
            $table->unsignedBigInteger('charge_department_id')->nullable()->after('department_id');
            $table->foreign('charge_department_id')->references('id')->on('departments')->onDelete('set null');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropForeign(['charge_department_id']);
            $table->dropColumn('charge_department_id');
        });
    }

};
