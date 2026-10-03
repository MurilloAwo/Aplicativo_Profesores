<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EntidadExterna extends Model
{
    use HasFactory;

    protected $table = 'entidades_externas';

    protected $fillable = ['nombre', 'nit', 'sector', 'ciudad', 'contacto'];

    public function actividades(): HasMany
    {
        return $this->hasMany(Actividad::class, 'entidad_externa_id');
    }
}
