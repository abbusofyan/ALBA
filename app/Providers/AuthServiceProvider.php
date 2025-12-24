<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\ThemeOption;
use App\Policies\ThemeOptionPolicy;
use App\Models\Menu;
use App\Policies\MenuPolicy;
use Spatie\Permission\Models\Role;
use App\Policies\RolePolicy;
use Spatie\Permission\Models\Permission;
use App\Policies\PermissionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        ThemeOption::class => ThemeOptionPolicy::class,
        Menu::class => MenuPolicy::class,
        Role::class => RolePolicy::class,
        Permission::class => PermissionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
