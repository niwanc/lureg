<?php

namespace App\Providers;

use App\Repositories\DocumentRepository;
use App\Repositories\DocumentRepositoryInterface;
use App\Repositories\SignatureRepository;
use App\Repositories\SignatureRepositoryInterface;
use App\Repositories\SignatureRequestRepository;
use App\Repositories\SignatureRequestRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;
use App\Services\DocumentService;
use App\Services\SignatureRequestService;
use App\Services\SignatureService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Laravel\Passport\Token;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserService::class, function ($app) {
            return new UserService($app->make(UserRepositoryInterface::class));
        });

        $this->app->bind(DocumentRepositoryInterface::class, DocumentRepository::class);
        $this->app->bind(DocumentService::class, function ($app) {
            return new DocumentService($app->make(DocumentRepositoryInterface::class));
        });

        $this->app->bind(SignatureRequestRepositoryInterface::class, SignatureRequestRepository::class);
        $this->app->bind(SignatureRequestService::class, function ($app) {
            return new SignatureRequestService($app->make(SignatureRequestRepositoryInterface::class));
        });

        $this->app->bind(SignatureRepositoryInterface::class, SignatureRepository::class);
        $this->app->bind(SignatureService::class, function ($app) {
            return new SignatureService($app->make(SignatureRepositoryInterface::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
        Passport::enablePasswordGrant();
    }

}
