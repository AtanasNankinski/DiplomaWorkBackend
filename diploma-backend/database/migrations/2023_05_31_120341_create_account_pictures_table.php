<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountPicturesTable extends Migration
{
    public function up()
    {
        Schema::create('account_pictures', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('color');
            $table->foreignId('user_id')->constrained('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('account_pictures');
    }
}