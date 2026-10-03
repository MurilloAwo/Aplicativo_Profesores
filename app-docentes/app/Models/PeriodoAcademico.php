<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodoAcademico extends Model
{
    use HasFactory;

    protected $table = 'periodos_academicos';

    protected $fillable = ['codigo', 'fecha_inicio', 'fecha_fin', 'activo', 'cerrado'];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
        'cerrado' => 'boolean',
    ];

    protected $attributes = [
        'activo' => false,
        'cerrado' => false,
    ];

    public function grupos(): HasMany
    {
        return $this->hasMany(Grupo::class, 'periodo_academico_id');
    }

    public function scopeActivo(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    /** Periodo activo actual (o null si no hay). */
    public static function actual(): ?self
    {
        return static::query()->activo()->first();
    }

    /** Se pueden registrar/editar actividades solo si está activo y no cerrado. */
    public function admiteRegistro(): bool
    {
        return $this->activo && ! $this->cerrado;
    }
}
