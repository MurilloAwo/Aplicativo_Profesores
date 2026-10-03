<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grupo extends Model
{
    use HasFactory;

    public const MODALIDADES = [
        'presencial' => 'Presencial',
        'virtual' => 'Virtual',
        'hibrida' => 'Híbrida',
    ];

    protected $table = 'grupos';

    protected $fillable = [
        'asignatura_id', 'periodo_academico_id', 'profesor_id', 'numero_grupo',
        'modalidad', 'horario', 'numero_estudiantes',
    ];

    protected $casts = [
        'numero_estudiantes' => 'integer',
    ];

    public function asignatura(): BelongsTo
    {
        return $this->belongsTo(Asignatura::class, 'asignatura_id');
    }

    public function periodoAcademico(): BelongsTo
    {
        return $this->belongsTo(PeriodoAcademico::class, 'periodo_academico_id');
    }

    public function profesor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'profesor_id');
    }

    /** Actividades donde este grupo es el principal. */
    public function actividadesPrincipales(): HasMany
    {
        return $this->hasMany(Actividad::class, 'grupo_id');
    }

    /** Todas las actividades que cubren este grupo (pivote actividad_grupo). */
    public function actividades(): BelongsToMany
    {
        return $this->belongsToMany(Actividad::class, 'actividad_grupo', 'grupo_id', 'actividad_id')
            ->withTimestamps();
    }

    public function getNombreAttribute(): string
    {
        return ($this->asignatura?->nombre ?? 'Asignatura').' — Grupo '.$this->numero_grupo;
    }
}
