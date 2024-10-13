<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\User;
use App\Traits\Cruds;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Type\Integer;

class AuthServiceProvider extends ServiceProvider
{
    use Cruds;

    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('admin-action', function(User $user){
            $tipo = $this->getUserTypeByUserId($user->id)->first();
            return $tipo->Etiqueta === 0;
        });

        Gate::define('user-owns-oferta', function(User $user, Request $request){
            LOG::info($user);
            LOG::info("ValidationInfo");
            LOG::info(json_encode($request));
            $userId = $user->id;
            $oferta = $this->findOfertaById($request->id);
            LOG::info("Found ".json_encode($oferta));
            LOG::info("Compare to ".$userId);
            LOG::info($oferta->id_usuario === $userId);
            return $oferta->id_usuario === $userId;
        });
    }
}
