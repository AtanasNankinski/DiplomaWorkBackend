<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('game_title');
            $table->string('game_description');
            $table->date('game_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
