<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoActividad extends Model
{
    use HasFactory;

    public const CATEGORIAS = [
        'relacion_sector_externo' => 'Relación con el sector externo',
        'innovacion_pedagogica' => 'Innovación pedagógica',
        'investigacion_formativa' => 'Investigación formativa',
        'extension' => 'Extensión',
        'gestion_curricular' => 'Gestión curricular',
        'otra' => 'Otra',
    ];

    protected $table = 'tipos_actividad';

    protected $fillable = ['nombre', 'descripcion', 'categoria', 'requiere_entidad_externa', 'activo'];

    protected $casts = [
        'requiere_entidad_externa' => 'boolean',
        'activo' => 'boolean',
    ];

    protected $attributes = [
        'categoria' => 'otra',
        'requiere_entidad_externa' => false,
        'activo' => true,
    ];

    public function actividades(): HasMany
    {
        return $this->hasMany(Actividad::class, 'tipo_actividad_id');
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function getCategoriaEtiquetaAttribute(): string
    {
        return self::CATEGORIAS[$this->categoria] ?? $this->categoria;
    }
}
