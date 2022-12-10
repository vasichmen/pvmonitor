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
        Schema::create('inverters', function (Blueprint $table) {
            $table->string('name')->primary();
            $table->double('vac');
            $table->double('pso');
            $table->double('paco');
            $table->double('pdco');
            $table->double('vdco');
            $table->double('c0');
            $table->double('c1');
            $table->double('c2');
            $table->double('c3');
            $table->double('pnt');
            $table->double('vdcmax');
            $table->double('idcmax');
            $table->double('mppt_low');
            $table->double('mppt_high');
            $table->date('cec_date')->nullable();
            $table->boolean('cec_hybrid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inverters');
    }
};
