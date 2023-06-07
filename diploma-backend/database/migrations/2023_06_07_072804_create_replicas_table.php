<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('replicas', function (Blueprint $table) {
            $table->id();
            $table->string('replica_name');
            $table->string('replica_type');
            $table->double('replica_power');
            $table->unsignedBigInteger('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('replicas');
    }
};