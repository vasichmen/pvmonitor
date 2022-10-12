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
        Schema::create('skill_confirmations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('skill_id');
            $table->uuid('user_id');
            $table->uuid('confirmator_id');
            $table->timestamps();

            $table->foreign('skill_id')->references('id')->on('skills');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('confirmator_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('skill_confirmations');
    }
};
