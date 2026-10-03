<?php

namespace App\Providers;

use App\Models\Actividad;
use App\Models\Evidencia;
use App\Models\Grupo;
use App\Policies\ActividadPolicy;
use App\Policies\EvidenciaPolicy;
use App\Policies\GrupoPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Grupo::class => GrupoPolicy::class,
        Actividad::class => ActividadPolicy::class,
        Evidencia::class => EvidenciaPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
