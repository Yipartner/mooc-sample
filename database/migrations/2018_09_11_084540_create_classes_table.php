<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('content');
            $table->integer('teacher_id');
            //课程押金费用
            $table->integer('fee');
            //最大押金退还额度
            $table->float('fee_back_num');
            //用于作业接口认证
            $table->string('access_token')->nullable();
            $table->string('class_secret')->nullable();
            $table->string('class_num');
            $table->timestamps();
            $table->unique('access_token');
            $table->unique('class_num');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('classes');
    }
}
