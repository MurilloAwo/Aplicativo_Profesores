<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CriterioAcreditacion extends Model
{
    use HasFactory;

    protected $table = 'criterios_acreditacion';

    protected $fillable = ['codigo', 'nombre', 'descripcion'];

    public function actividades(): BelongsToMany
    {
        return $this->belongsToMany(Actividad::class, 'actividad_criterio', 'criterio_acreditacion_id', 'actividad_id')
            ->withTimestamps();
    }
}
