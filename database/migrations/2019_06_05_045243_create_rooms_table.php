<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            // 房间id
            $table->increments('id');
            // 房间名称
            $table->string('name', 64)->default('这是一个房间');
            // 房间描述
            $table->string('description', 512)->default('大家一起愉快相处吧！');
            // 房间最大在线人数
            $table->integer('size')->default(10);
            // 房主id
            $table->integer('owner_id')->unsigned();
            $table->foreign('owner_id')->references('id')->on('users');
            // 音乐插件是否开启
            $table->boolean('open')->default(false);
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
        Schema::dropIfExists('rooms');
    }
}
