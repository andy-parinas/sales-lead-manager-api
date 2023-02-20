<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Reports\Interfaces\SalesStaffLeadSummaryReport;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use App\Traits\ReportComputer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesStaffLeadSummaryReportController extends ApiController
{

    use ReportComputer;

    protected $reportRepository;
    protected $salesStaffLeadSummaryReport;

    public function __construct(ReportRepositoryInterface $reportRepository,
    SalesStaffLeadSummaryReport $salesStaffLeadSummaryReport)
    {
        $this->middleware('auth:sanctum');
        $this->reportRepository = $reportRepository;
        $this->salesStaffLeadSummaryReport = $salesStaffLeadSummaryReport;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        if($request->has('start_date') && $request->has('end_date')){

            $results = [];

            if($user->user_type == User::HEAD_OFFICE){
                $results = $this->salesStaffLeadSummaryReport->generate($request->all());
            }else {
                $franchiseIds = $user->franchises->pluck('id')->toArray();
                $results = $this->salesStaffLeadSummaryReport->generateByFranchise($franchiseIds, $request->all());
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
