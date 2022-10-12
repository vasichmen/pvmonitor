<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_accesses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('material_id')->index();
            $table->uuid('access_class_id')->index();
            $table->uuid('access_right_id')->index();
            $table->timestamps();

            $table->foreign('access_class_id')->references('id')->on('access_classes');
            $table->foreign('access_right_id')->references('id')->on('access_rights');
            $table->foreign('material_id')->references('id')->on('materials');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('material_accesses');
    }
};
