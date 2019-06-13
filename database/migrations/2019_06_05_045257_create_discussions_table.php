<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscussionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discussions', function (Blueprint $table) {
            $table->increments('id');
            // 发送人id
            $table->integer('send_id')->unsigned();
            // $table->foreign('send_id')->references('id')->on('users');
            // 接受人id
            $table->integer('receive_id')->unsigned()->default('1');
            // $table->foreign('receive_id')->references('id')->on('users');
            // 房间id
            $table->integer('room_id')->unsigned();
            // $table->foreign('room_id')->references('id')->on('rooms');
            // 内容
            $table->text('message', 512);
            // 时间戳
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
        Schema::dropIfExists('discussions');
    }
}
