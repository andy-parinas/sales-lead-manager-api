<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Reports\Interfaces\LeadAndContractDateReport;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadAndContractDateReportController extends ApiController
{
    protected $reportRepository;
    private $leadAndContractDateReport;

    public function __construct(
        ReportRepositoryInterface $reportRepository,
        LeadAndContractDateReport $leadAndContractDateReport
    )
    {
        $this->middleware('auth:sanctum');
        $this->reportRepository = $reportRepository;
        $this->leadAndContractDateReport = $leadAndContractDateReport;
    }


    public function index(Request $request)
    {
        $user = Auth::user();

        if($request->has('start_date') && $request->has('end_date')){
            
            $results = [];

            if($user->user_type == User::HEAD_OFFICE){
                $results = $this->leadAndContractDateReport->generate($request->all());
            }else {
                $franchiseIds = $user->franchises->pluck('id')->toArray();
                $results =  $this->leadAndContractDateReport->generateByFranchise($franchiseIds, $request->all());
            }

            if($results->count() > 0){
                return $this->showOne([
                    'results' => $results,
                    'total' => $results->count()
                ]);
            }

            return $this->showOne([
                'results' => $results
            ]);
        }
    }
}
