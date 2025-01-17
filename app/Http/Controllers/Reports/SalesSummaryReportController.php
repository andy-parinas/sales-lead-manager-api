<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Reports\Interfaces\SalesStaffProductReport;
use App\Reports\Interfaces\SalesStaffSummaryReport;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use App\Traits\ReportComputer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesSummaryReportController extends ApiController
{

    use ReportComputer;

    protected $reportRepository;
    protected $salesStaffProductReport;

    public function __construct(ReportRepositoryInterface $reportRepository,
                                SalesStaffProductReport $salesStaffProductReport)
    {
        $this->middleware('auth:sanctum');
        $this->reportRepository = $reportRepository;
        $this->salesStaffProductReport = $salesStaffProductReport;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        if($request->has('start_date') && $request->has('end_date')){

            $results = [];

            if($user->user_type == User::HEAD_OFFICE){
                #$results = $this->reportRepository->generateSalesStaffProductSummary($request->all());
                $results = $this->salesStaffProductReport->generate($request->all());
            }else {
                $franchiseIds = $user->franchises->pluck('id')->toArray();

                #$results = $this->salesStaffProductReport->gen($franchiseIds, $request->all());
                $results = $this->salesStaffProductReport->generateByFranchise($franchiseIds, $request->all());
            }


            if($results->count() > 0){
                $total = $this->computeTotal($results);

                return $this->showOne([
                    'results' => $results,
                    'total' => $total
                ]);

            }

            return $this->showOne([
                'results' => $results
            ]);
        }
    }

}
