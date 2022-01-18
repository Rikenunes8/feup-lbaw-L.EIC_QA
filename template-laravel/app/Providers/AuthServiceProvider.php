<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Notifications\ResetPassword;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
      'App\Models\Intervention' => 'App\Policies\InterventionPolicy',
      'App\Models\Uc' => 'App\Policies\UcPolicy',
      'App\Models\User' => 'App\Policies\UserPolicy',
      'App\Models\Notification' => 'App\Policies\NotificationPolicy'
    ];

    /**
     * Allowed email domains for user registration
     *
     * @var array
     */
    protected $allowedDomains = [
      'edu.fe.up.pt',
      'edu.fc.up.pt',
      'fe.up.pt',
      'fc.up.pt',
      'up.pt',
      'fe.up.qa.pt',
      'fc.up.qa.pt',
      'up.qa.pt',
      'g.uporto.pt',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Validator::extend('allowed_domain', function($attribute, $value, $parameters, $validator) {
          return in_array(explode('@', $value)[1], $this->allowedDomains);
        }, 'Domain not valid for registration. Valid Domains: @fe.up.pt, @fc.up.pt, @up.pt, @g.uporto.pt.');
    }
}
