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

        Gate::define('user-owns-demanda', function(User $user, Request $request){
            LOG::info($user);
            LOG::info("ValidationInfo");
            LOG::info(json_encode($request));
            $userId = $user->id;
            $demanda = $this->findDemandaById($request->id);
            LOG::info("Found ".json_encode($demanda));
            LOG::info("Compare to ".$userId);
            LOG::info($demanda->id_usuario === $userId);
            return $demanda->id_usuario === $userId;
        });

        Gate::define('user-owns-publicacion', function(User $user, Request $request){
            LOG::info("Check publicacion ownership");
            return $this->checkPublicacionOwnership($user->id, $request->id_publicacion) !== null;
        });

        Gate::define('user-owns-image', function(User $user, Request $request){
            LOG::info("USER ". $user->id." image ".$request->id_image);
            LOG::info("Validate image ownership");
            $ownership = $this->findImageOwnership($user->id, $request->id_image);
            LOG::info("found image ownership".json_encode($ownership));
            return $ownership !== null;
        });

        Gate::define('can-user-buy', function(User $user, $costo){
            LOG::info("In can user buy validation");
            $balance = $this->balance($user->id);
            return $balance >= $costo;
        });
    }
}
