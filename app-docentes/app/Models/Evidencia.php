<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evidencia extends Model
{
    use HasFactory;

    public const TIPOS = [
        'foto' => 'Foto',
        'acta' => 'Acta',
        'lista_asistencia' => 'Lista de asistencia',
        'certificado' => 'Certificado',
        'otro' => 'Otro',
    ];

    protected $table = 'evidencias';

    protected $fillable = ['actividad_id', 'tipo', 'nombre_original', 'ruta', 'mime', 'tamano'];

    /** La ruta interna no se expone al serializar. */
    protected $hidden = ['ruta'];

    protected $casts = [
        'tamano' => 'integer',
    ];

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class, 'actividad_id');
    }

    public function getTipoEtiquetaAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }
}
