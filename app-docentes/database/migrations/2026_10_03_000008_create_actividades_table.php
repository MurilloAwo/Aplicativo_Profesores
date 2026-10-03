<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * grupo_id = grupo principal (dueño de la actividad).
     * La pivote actividad_grupo contiene TODOS los grupos que cubre (incluido el principal).
     */
    public function up(): void
    {
        Schema::create('actividades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_id')->constrained('grupos')->restrictOnDelete();
            $table->foreignId('tipo_actividad_id')->constrained('tipos_actividad')->restrictOnDelete();
            $table->foreignId('entidad_externa_id')->nullable()->constrained('entidades_externas')->nullOnDelete();
            $table->string('titulo');
            $table->text('descripcion');
            $table->text('objetivo')->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->decimal('duracion_horas', 6, 2)->default(0);
            $table->string('lugar')->nullable();
            $table->enum('modalidad', ['presencial', 'virtual', 'hibrida'])->default('presencial');
            $table->unsignedInteger('numero_estudiantes_participantes')->default(0);
            $table->string('nombre_invitado')->nullable();
            $table->string('cargo_invitado')->nullable();
            $table->text('resultados_obtenidos')->nullable();
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['borrador', 'registrada'])->default('borrador')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('actividad_grupo', function (Blueprint $table) {
            $table->foreignId('actividad_id')->constrained('actividades')->cascadeOnDelete();
            $table->foreignId('grupo_id')->constrained('grupos')->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['actividad_id', 'grupo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividad_grupo');
        Schema::dropIfExists('actividades');
    }
};
