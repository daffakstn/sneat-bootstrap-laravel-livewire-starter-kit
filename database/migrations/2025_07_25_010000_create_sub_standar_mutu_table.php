<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sub_standar_mutu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('standar_mutu_id')->constrained('standar_mutu')->onDelete('cascade');
            $table->foreignId('prodi_id')->constrained('prodi')->onDelete('restrict');
            $table->string('sub_standar');
            $table->text('indikator_0');
            $table->text('indikator_1');
            $table->text('indikator_2');
            $table->text('indikator_3');
            $table->text('indikator_4');
            $table->text('target')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sub_standar_mutu');
    }
};