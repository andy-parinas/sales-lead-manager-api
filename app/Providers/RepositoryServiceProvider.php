<?php

namespace App\Providers;

use App\Reports\Interfaces\LeadAndContractReport;
use App\Reports\Interfaces\LeadAndContractDateReport;
use App\Reports\Interfaces\ProductSalesSummaryReport;
use App\Reports\Interfaces\SalesContractReport;
use App\Reports\Interfaces\SalesStaffLeadSummaryReport;
use App\Reports\Interfaces\SalesStaffProductReport;
use App\Reports\Interfaces\SalesStaffSummaryReport;
use App\Reports\Interfaces\AppointmentReport;
use App\Reports\LeadAndContractReportImp;
use App\Reports\LeadAndContractDateReportImp;
use App\Reports\ProductSalesSummaryReportImp;
use App\Reports\SalesContractReportImp;
use App\Reports\SalesStaffLeadSummaryReportImp;
use App\Reports\SalesStaffProductReportImp;
use App\Reports\SalesStaffSummaryReportImp;
use App\Reports\AppointmentReportImp;
use App\Repositories\CustomerReviewReportRepository;
use App\Repositories\DocumentRepository;
use App\Repositories\FranchiseRepository;
use App\Repositories\Interfaces\CustomerReviewReportInterface;
use App\Repositories\Interfaces\DocumentRepositoryInterface;
use App\Repositories\Interfaces\FranchiseRepositoryInterface;
use App\Repositories\Interfaces\LeadRepositoryInterface;
use App\Repositories\Interfaces\LeadTransferRepositoryInterface;
use App\Repositories\Interfaces\PostcodeRepositoryInterface;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use App\Repositories\Interfaces\SalesContactRepositoryInterface;
use App\Repositories\Interfaces\SalesStafRepositoryInterface;
use App\Repositories\Interfaces\TradeStaffRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\LeadRepository;
use App\Repositories\LeadTransferRepository;
use App\Repositories\PostcodeRepository;
use App\Repositories\ReportRepository;
use App\Repositories\SalesContactRepository;
use App\Repositories\SalesStaffRepository;
use App\Repositories\TradeStaffRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        $this->app->bind(FranchiseRepositoryInterface::class, FranchiseRepository::class);
        $this->app->bind(LeadRepositoryInterface::class, LeadRepository::class);
        $this->app->bind(LeadTransferRepositoryInterface::class, LeadTransferRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(SalesContactRepositoryInterface::class, SalesContactRepository::class);
        $this->app->bind(DocumentRepositoryInterface::class, DocumentRepository::class);
        $this->app->bind(SalesStafRepositoryInterface::class, SalesStaffRepository::class);
        $this->app->bind(PostcodeRepositoryInterface::class, PostcodeRepository::class);
        $this->app->bind(TradeStaffRepositoryInterface::class, TradeStaffRepository::class);
        $this->app->bind(ReportRepositoryInterface::class, ReportRepository::class);
        $this->app->bind(CustomerReviewReportInterface::class, CustomerReviewReportRepository::class);
        $this->app->bind(SalesStaffLeadSummaryReport::class, SalesStaffLeadSummaryReportImp::class);
        $this->app->bind(SalesStaffSummaryReport::class, SalesStaffSummaryReportImp::class);
        $this->app->bind(SalesStaffProductReport::class, SalesStaffProductReportImp::class);
        $this->app->bind(SalesContractReport::class, SalesContractReportImp::class);
        $this->app->bind(ProductSalesSummaryReport::class, ProductSalesSummaryReportImp::class);
        $this->app->bind(LeadAndContractReport::class, LeadAndContractReportImp::class);
        $this->app->bind(LeadAndContractDateReport::class, LeadAndContractDateReportImp::class);
        $this->app->bind(AppointmentReport::class, AppointmentReportImp::class);   
    }
}
