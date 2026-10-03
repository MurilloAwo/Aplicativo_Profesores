<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_actividad', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->text('descripcion')->nullable();
            $table->enum('categoria', [
                'relacion_sector_externo',
                'innovacion_pedagogica',
                'investigacion_formativa',
                'extension',
                'gestion_curricular',
                'otra',
            ])->default('otra');
            $table->boolean('requiere_entidad_externa')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_actividad');
    }
};
