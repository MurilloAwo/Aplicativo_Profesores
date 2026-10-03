<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asignatura extends Model
{
    use HasFactory;

    protected $table = 'asignaturas';

    protected $fillable = ['codigo', 'nombre', 'creditos', 'tipologia', 'programa_curricular_id'];

    protected $casts = [
        'creditos' => 'integer',
    ];

    public function programaCurricular(): BelongsTo
    {
        return $this->belongsTo(ProgramaCurricular::class, 'programa_curricular_id');
    }

    public function grupos(): HasMany
    {
        return $this->hasMany(Grupo::class, 'asignatura_id');
    }
}
