<?php

use App\Traits\PartitionedByHash;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    use PartitionedByHash;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->makePartitionedTable('searches', 100, function (Blueprint $table) {
            $table->uuid('user_id')->nullable();
            $table->json('params');
            $table->json('result');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('searches');
    }
};
