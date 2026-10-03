<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Actividad extends Model
{
    use HasFactory, SoftDeletes;

    public const ESTADO_BORRADOR = 'borrador';

    public const ESTADO_REGISTRADA = 'registrada';

    public const ESTADOS = [
        self::ESTADO_BORRADOR => 'Borrador',
        self::ESTADO_REGISTRADA => 'Registrada',
    ];

    public const MODALIDADES = Grupo::MODALIDADES;

    protected $table = 'actividades';

    protected $fillable = [
        'grupo_id', 'tipo_actividad_id', 'entidad_externa_id', 'titulo', 'descripcion', 'objetivo',
        'fecha_inicio', 'fecha_fin', 'duracion_horas', 'lugar', 'modalidad',
        'numero_estudiantes_participantes', 'nombre_invitado', 'cargo_invitado',
        'resultados_obtenidos', 'observaciones', 'estado',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'duracion_horas' => 'decimal:2',
        'numero_estudiantes_participantes' => 'integer',
    ];

    protected $attributes = [
        'estado' => self::ESTADO_BORRADOR,
        'modalidad' => 'presencial',
    ];

    /** Grupo principal (dueño de la actividad). */
    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }

    /** Todos los grupos cubiertos (incluye el principal). */
    public function grupos(): BelongsToMany
    {
        return $this->belongsToMany(Grupo::class, 'actividad_grupo', 'actividad_id', 'grupo_id')
            ->withTimestamps();
    }

    public function tipoActividad(): BelongsTo
    {
        return $this->belongsTo(TipoActividad::class, 'tipo_actividad_id');
    }

    public function entidadExterna(): BelongsTo
    {
        return $this->belongsTo(EntidadExterna::class, 'entidad_externa_id');
    }

    public function evidencias(): HasMany
    {
        return $this->hasMany(Evidencia::class, 'actividad_id');
    }

    public function criterios(): BelongsToMany
    {
        return $this->belongsToMany(CriterioAcreditacion::class, 'actividad_criterio', 'actividad_id', 'criterio_acreditacion_id')
            ->withTimestamps();
    }

    /** Actividades cuyo grupo principal pertenece al profesor. */
    public function scopeDelProfesor(Builder $query, User|int $profesor): Builder
    {
        $id = $profesor instanceof User ? $profesor->id : $profesor;

        return $query->whereHas('grupo', fn (Builder $q) => $q->where('profesor_id', $id));
    }

    public function scopeDelPeriodo(Builder $query, PeriodoAcademico|int $periodo): Builder
    {
        $id = $periodo instanceof PeriodoAcademico ? $periodo->id : $periodo;

        return $query->whereHas('grupo', fn (Builder $q) => $q->where('periodo_academico_id', $id));
    }

    public function getEstadoEtiquetaAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }
}
