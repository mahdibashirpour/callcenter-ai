<?php

namespace App\Providers;

use App\Application\Crm\CrmManager;
use App\Domain\Crm\Contracts\CrmConnectionRepositoryInterface;
use App\Domain\Crm\Contracts\CrmLogRepositoryInterface;
use App\Infrastructure\Crm\CrmAdapterRegistry;
use App\Infrastructure\Crm\Repositories\EloquentCrmConnectionRepository;
use App\Infrastructure\Crm\Repositories\EloquentCrmLogRepository;
use Illuminate\Support\ServiceProvider;

class CrmServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CrmAdapterRegistry::class);

        $this->app->bind(CrmConnectionRepositoryInterface::class, EloquentCrmConnectionRepository::class);
        $this->app->bind(CrmLogRepositoryInterface::class, EloquentCrmLogRepository::class);

        $this->app->bind(CrmManager::class, function ($app) {
            return new CrmManager(
                resolver: $app->make(CrmConnectionResolver::class),
                logs: $app->make(CrmLogRepositoryInterface::class),
            );
        });
    }
}
