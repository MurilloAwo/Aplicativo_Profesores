<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catálogo vacío por defecto: el admin carga los factores/características vigentes.
     */
    public function up(): void
    {
        Schema::create('criterios_acreditacion', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('actividad_criterio', function (Blueprint $table) {
            $table->foreignId('actividad_id')->constrained('actividades')->cascadeOnDelete();
            $table->foreignId('criterio_acreditacion_id')->constrained('criterios_acreditacion')->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['actividad_id', 'criterio_acreditacion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actividad_criterio');
        Schema::dropIfExists('criterios_acreditacion');
    }
};
