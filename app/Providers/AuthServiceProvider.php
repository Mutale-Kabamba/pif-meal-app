<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        Gate::define('view_global_metrics', function (User $user) {
            return $user->isHeadOfProgrammes();
        });

        Gate::define('export_data', function (User $user) {
            return $user->isHeadOfProgrammes() || $user->isSystemManager();
        });

        Gate::define('manage_projects', function (User $user) {
            return $user->isHeadOfProgrammes() || $user->isSystemManager();
        });

        Gate::define('manage_beneficiaries', function (User $user) {
            return $user->isSystemManager();
        });

        Gate::define('generate_cards', function (User $user) {
            return $user->isSystemManager();
        });

        Gate::define('access_terminal', function (User $user) {
            return $user->isCook();
        });

        Gate::define('view_admin', function (User $user) {
            return $user->isHeadOfProgrammes() || $user->isSystemManager();
        });
    }
}
