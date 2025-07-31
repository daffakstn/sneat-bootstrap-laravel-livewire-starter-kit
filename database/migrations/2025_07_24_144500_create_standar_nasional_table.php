<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('standar_nasional', function (Blueprint $table) {
            $table->id();
            $table->string('standar', 50);
            $table->text('keterangan')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('standar_nasional')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('standar_nasional');
    }
};