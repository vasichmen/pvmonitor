<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solar_insolations', function (Blueprint $table) {
            $table->double('lat');
            $table->double('lon');
            $table->double('full');
            $table->double('full_optimal');
            $table->double('direct');
            $table->double('diffuse');
            $table->double('altitude');
            $table->timestamps();

            $table->primary(['lat', 'lon']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('solar_insolations');
    }
};
