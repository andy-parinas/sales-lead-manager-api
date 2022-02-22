<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Reports\Interfaces\SalesStaffSummaryReport;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use App\Traits\ReportComputer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesStaffSummaryReportController extends ApiController
{

    use ReportComputer;

    protected $reportRepository;
    protected $salesStaffSummaryReport;

    public function __construct(ReportRepositoryInterface $reportRepository,
                                SalesStaffSummaryReport $salesStaffSummaryReport)
    {
        $this->middleware('auth:sanctum');
        $this->reportRepository = $reportRepository;
        $this->salesStaffSummaryReport = $salesStaffSummaryReport;
    }

    public function index(Request $request)
    {

        $user = Auth::user();

        if($request->has('start_date') && $request->has('end_date')){

            $results = [];

            // $results = $this->salesStaffSummaryReport->generate($request->all());

            if($user->user_type == User::HEAD_OFFICE){

                #$results = $this->reportRepository->generateSalesSummary($request->all());
                $results = $this->salesStaffSummaryReport->generate($request->all());

            }else {

                $franchiseIds = $user->franchises->pluck('id')->toArray();

                #$results = $this->reportRepository->generateSalesSummaryByFranchises($franchiseIds, $request->all());
                $results = $this->salesStaffSummaryReport->generateByFranchise($franchiseIds, $request->all());
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
