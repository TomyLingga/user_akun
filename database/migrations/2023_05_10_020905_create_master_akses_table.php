<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterAksesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_akses', function (Blueprint $table) {
            $table->id('akses_id');
            $table->integer('app_id');
            $table->integer('user_id');
            $table->integer('level_akses'); //null/0 = unauthorized, 1 read, 2 write, 3 edit, 4 delete
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
        Schema::dropIfExists('master_akses');
    }
}
