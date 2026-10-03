<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asignatura_id')->constrained('asignaturas')->restrictOnDelete();
            $table->foreignId('periodo_academico_id')->constrained('periodos_academicos')->restrictOnDelete();
            $table->foreignId('profesor_id')->constrained('users')->restrictOnDelete();
            $table->string('numero_grupo', 10);
            $table->enum('modalidad', ['presencial', 'virtual', 'hibrida'])->default('presencial');
            $table->string('horario')->nullable();
            $table->unsignedSmallInteger('numero_estudiantes')->default(0);
            $table->timestamps();

            $table->unique(['asignatura_id', 'periodo_academico_id', 'numero_grupo'], 'grupos_asig_periodo_numero_unique');
            $table->index(['profesor_id', 'periodo_academico_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }
};
