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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('type', ['user', 'contact']);
            $table->uuid('author_id')->nullable();
            $table->integer('version')->nullable();
            $table->string('external_id')->nullable();
            $table->string('name');
            $table->string('last_name')->nullable();
            $table->string('second_name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->integer('phone')->nullable();
            $table->string('position')->nullable();
            $table->boolean('active')->default(true);
            $table->dateTime('last_auth')->default(null)->nullable();
            $table->string('password')->nullable();
            $table->uuid('department_id')->nullable();
            $table->uuid('avatar_id')->nullable();
            $table->uuid('photo_id')->nullable();

            $table->timestamps();

            $table->foreign('department_id')->references('id')->on('departments');
            $table->foreign('avatar_id')->references('id')->on('files');
            $table->foreign('photo_id')->references('id')->on('files');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
