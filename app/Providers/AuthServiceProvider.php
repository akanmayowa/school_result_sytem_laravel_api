<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{

    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];


    public function boot()
    {
        $this->registerPolicies();
        $this->userRolePermission();
    }

    public function userRolePermission(){
        Gate::define('isSuperAdmin', function($user) { return $user->user_role == 'super_admin'; });
        Gate::define('isAdmin', function($user) { return $user->user_role == 'admin'; });
        Gate::define('isSchoolAdmin', function($user) { return $user->user_role == 'school_admin'; });
        Gate::define('isSchoolAdmin', function($user) { return $user->user_role == 'training_school_admin'; });

    }
}
