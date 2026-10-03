<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramaCurricular extends Model
{
    use HasFactory;

    public const NIVELES = [
        'pregrado' => 'Pregrado',
        'posgrado' => 'Posgrado',
    ];

    protected $table = 'programas_curriculares';

    protected $fillable = ['codigo', 'nombre', 'nivel'];

    public function asignaturas(): HasMany
    {
        return $this->hasMany(Asignatura::class, 'programa_curricular_id');
    }
}
