<?php

namespace App\Providers;

use App\Reports\Interfaces\ProductSalesSummaryReport;
use App\Reports\Interfaces\SalesContractReport;
use App\Reports\Interfaces\SalesStaffProductReport;
use App\Reports\Interfaces\SalesStaffSummaryReport;
use App\Reports\ProductSalesSummaryReportImp;
use App\Reports\SalesContractReportImp;
use App\Reports\SalesStaffProductReportImp;
use App\Reports\SalesStaffSummaryReportImp;
use App\Repositories\DocumentRepository;
use App\Repositories\Interfaces\DocumentRepositoryInterface;
use App\Services\ContractFinanceService;
use App\Services\FranchiseService;
use App\Services\Interfaces\ContractFinanceServiceInterface;
use App\Services\Interfaces\EmailServiceInterface;
use App\Services\Interfaces\FranchiseServiceInterface;
use App\Services\Interfaces\PostcodeServiceInterface;
use App\Services\Interfaces\SmsServiceInterface;
use App\Services\MessageMediaService;
use App\Services\PostcodeService;
use App\Services\PostmarkService;
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
        $this->app->bind(SmsServiceInterface::class, MessageMediaService::class);
        $this->app->bind(EmailServiceInterface::class, PostmarkService::class);
        $this->app->bind(SalesStaffSummaryReport::class, SalesStaffSummaryReportImp::class);
        $this->app->bind(SalesStaffProductReport::class, SalesStaffProductReportImp::class);
        $this->app->bind(ProductSalesSummaryReport::class, ProductSalesSummaryReportImp::class);
        $this->app->bind(SalesContractReport::class, SalesContractReportImp::class);
    }
}
