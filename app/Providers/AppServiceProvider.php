<?php

namespace App\Providers;

use App\Models\Organization;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Cashier::useCustomerModel(Organization::class);

        RateLimiter::for('organization-api', function (Request $request) {
            $organizationId = $request->user()?->organization_id;

            return Limit::perMinute(60)->by(
                $organizationId ? "org:{$organizationId}" : $request->ip()
            );
        });
    }
}
