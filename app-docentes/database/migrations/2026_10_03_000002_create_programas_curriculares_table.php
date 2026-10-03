<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programas_curriculares', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre');
            $table->enum('nivel', ['pregrado', 'posgrado']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programas_curriculares');
    }
};
