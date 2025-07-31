<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('standar_mutu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_id')->constrained('tahun')->onDelete('restrict');
            $table->foreignId('lembaga_akreditasi_id')->constrained('lembaga_akreditasi')->onDelete('restrict');
            $table->foreignId('standar_nasional_id')->constrained('standar_nasional')->onDelete('restrict');
            $table->enum('status', ['aktif', 'draft', 'nonaktif'])->default('draft');
            $table->decimal('nilai_mutu', 3, 2)->nullable()->comment('Nilai mutu maksimal 4.00');
            $table->string('bukti_dokumen')->nullable()->comment('Path file PDF atau link Google Drive');
            $table->text('komentar_auditee')->nullable()->comment('Komentar dari Auditee');
            $table->text('komentar_auditor')->nullable()->comment('Komentar dari Auditor');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('standar_mutu');
    }
};
