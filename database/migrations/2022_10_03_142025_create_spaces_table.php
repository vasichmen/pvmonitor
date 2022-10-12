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
        Schema::create('spaces', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->enum('type', ['group', 'space']);
            $table->text('description')->nullable();
            $table->string('template_code')->nullable();
            $table->json('parents')->nullable();
            $table->uuid('parent_id')->nullable();
            $table->uuid('owner_id');
            $table->uuid('group_access_type_id')->nullable();
            $table->uuid('space_status_id')->nullable();
            $table->uuid('logo_id')->nullable();
            $table->uuid('image_id')->nullable();
            $table->boolean('active')->default(true);
            $table->json('appearance')->nullable();
            $table->json('overview_page_params')->nullable();
            $table->string('about')->nullable();
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('users');
            $table->foreign('group_access_type_id')->references('id')->on('group_access_types');
            $table->foreign('space_status_id')->references('id')->on('space_statuses');
            $table->foreign('logo_id')->references('id')->on('files');
            $table->foreign('image_id')->references('id')->on('files');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spaces');
    }
};
