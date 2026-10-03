<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evidencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actividad_id')->constrained('actividades')->cascadeOnDelete();
            $table->enum('tipo', ['foto', 'acta', 'lista_asistencia', 'certificado', 'otro'])->default('otro');
            $table->string('nombre_original');
            $table->string('ruta'); // ruta relativa en el disco "local" (privado)
            $table->string('mime', 150);
            $table->unsignedBigInteger('tamano'); // bytes
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidencias');
    }
};
