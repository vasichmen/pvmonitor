<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    use \App\Traits\PartitionedByHash;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->makePartitionedTable('materials', 100, function (Blueprint $table) {
            $table->string('name');
            $table->uuid('owner_id');
            $table->uuid('author_id');
            $table->uuid('modifier_id');
            $table->uuid('material_status_id');
            $table->uuid('material_type_id');
            $table->text('description');
            $table->json('parent_spaces');
            $table->integer('version');
            $table->boolean('deleted')->default(false);
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('users');
            $table->foreign('modifier_id')->references('id')->on('users');
            $table->foreign('author_id')->references('id')->on('users');
            $table->foreign('material_status_id')->references('id')->on('material_statuses');
            $table->foreign('material_type_id')->references('id')->on('material_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('materials');
    }
};
