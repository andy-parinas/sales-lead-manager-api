<?php

namespace App\Providers;

use App\Repositories\DocumentRepository;
use App\Repositories\Interfaces\DocumentRepositoryInterface;
use App\Services\ContractFinanceService;
use App\Services\FranchiseService;
use App\Services\Interfaces\ContractFinanceServiceInterface;
use App\Services\Interfaces\FranchiseServiceInterface;
use App\Services\Interfaces\PostcodeServiceInterface;
use App\Services\PostcodeService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(PostcodeServiceInterface::class, PostcodeService::class);
        $this->app->bind(FranchiseServiceInterface::class, FranchiseService::class);
        $this->app->bind(ContractFinanceServiceInterface::class, ContractFinanceService::class);
    }
}
