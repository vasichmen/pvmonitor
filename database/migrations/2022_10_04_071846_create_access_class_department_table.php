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
        Schema::create('access_class_department', function (Blueprint $table) {
            $table->uuid('access_class_id')->index();
            $table->uuid('department_id')->index();

            $table->foreign('access_class_id')->references('id')->on('access_classes');
            $table->foreign('department_id')->references('id')->on('departments');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('access_class_department');
    }
};
