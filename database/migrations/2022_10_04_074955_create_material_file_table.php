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
        Schema::create('material_file', function (Blueprint $table) {
            $table->uuid('material_id')->index();
            $table->uuid('file_id')->index();

            $table->foreign('material_id')->references('id')->on('materials');
            $table->foreign('file_id')->references('id')->on('files');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('material_file');
    }
};
