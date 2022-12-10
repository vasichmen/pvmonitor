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
        Schema::create('modules', function (Blueprint $table) {
            $table->string('name')->primary();
            $table->string('manufacturer');
            $table->string('technology');
            $table->double('bifacial');
            $table->double('stc');
            $table->double('ptc');
            $table->double('a_c');
            $table->double('length');
            $table->double('width');
            $table->double('n_s');
            $table->double('i_sc_ref');
            $table->double('v_oc_ref');
            $table->double('i_mp_ref');
            $table->double('v_mp_ref');
            $table->double('alpha_sc');
            $table->double('beta_oc');
            $table->double('t_noct');
            $table->double('a_ref');
            $table->double('i_l_ref');
            $table->double('i_o_ref');
            $table->double('r_s');
            $table->double('r_sh_ref');
            $table->double('adjust');
            $table->double('gamma_r');
            $table->boolean('bipv');
            $table->string('version');
            $table->date('date')->nullable();


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
        Schema::dropIfExists('modules');
    }
};
