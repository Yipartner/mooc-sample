<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVersionControlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('version_control', function (Blueprint $table) {
            $table->increments('id');
            $table->string('version_num');
            $table->text('version_content');
            $table->dateTime('updated_at');
            $table->string('apk_url');
            //student | teacher
            $table->string('client');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('version_control');
    }
}
