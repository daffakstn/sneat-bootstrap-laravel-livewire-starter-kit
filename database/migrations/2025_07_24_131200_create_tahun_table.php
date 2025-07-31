<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tahun', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun')->unique();
            $table->enum('status', ['aktif', 'nonaktif'])->default('nonaktif');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tahun');
    }
};