<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROL_ADMIN = 'admin';

    public const ROL_PROFESOR = 'profesor';

    public const ROLES = [
        self::ROL_ADMIN => 'Administrador',
        self::ROL_PROFESOR => 'Profesor',
    ];

    public const TIPOS_VINCULACION = [
        'planta' => 'Planta',
        'ocasional' => 'Ocasional',
        'catedra' => 'Cátedra',
    ];

    /**
     * The attributes that are mass assignable.
     * `rol` y `activo` NO son asignables masivamente: el admin los fija explícitamente
     * (evita escalamiento de privilegios desde formularios).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'nombres',
        'apellidos',
        'documento',
        'email',
        'password',
        'tipo_vinculacion',
        'dedicacion',
        'categoria',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'activo' => 'boolean',
    ];

    protected $attributes = [
        'rol' => self::ROL_PROFESOR,
        'activo' => true,
    ];

    protected static function booted(): void
    {
        // `name` (usado por Breeze) se sincroniza con nombres y apellidos
        static::saving(function (User $user) {
            if ($user->isDirty(['nombres', 'apellidos'])) {
                $completo = trim(($user->nombres ?? '').' '.($user->apellidos ?? ''));
                if ($completo !== '') {
                    $user->name = $completo;
                }
            } elseif ($user->isDirty('name') && ! empty($user->name)) {
                $partes = explode(' ', trim($user->name), 2);
                $user->nombres = $partes[0];
                $user->apellidos = $partes[1] ?? '';
            }
        });
    }

    public function grupos(): HasMany
    {
        return $this->hasMany(Grupo::class, 'profesor_id');
    }

    /** Actividades cuyo grupo principal pertenece a este profesor. */
    public function actividades(): HasManyThrough
    {
        return $this->hasManyThrough(Actividad::class, Grupo::class, 'profesor_id', 'grupo_id');
    }

    public function esAdmin(): bool
    {
        return $this->rol === self::ROL_ADMIN;
    }

    public function esProfesor(): bool
    {
        return $this->rol === self::ROL_PROFESOR;
    }

    public function getNombreCompletoAttribute(): string
    {
        return $this->name;
    }
}
