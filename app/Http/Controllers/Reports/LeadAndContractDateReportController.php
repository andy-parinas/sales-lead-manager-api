<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Reports\Interfaces\LeadAndContractDateReport;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use App\Repositories\Interfaces\FranchiseRepositoryInterface;
use App\Traits\LeadAndContractDateComputer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadAndContractDateReportController extends ApiController
{
    use LeadAndContractDateComputer;
    protected $reportRepository;
    private $leadAndContractDateReport;
    protected $franchiseRepository;

    public function __construct(
        ReportRepositoryInterface $reportRepository,
        LeadAndContractDateReport $leadAndContractDateReport,
        FranchiseRepositoryInterface $franchiseRepository
    )
    {
        $this->middleware('auth:sanctum');
        $this->reportRepository = $reportRepository;
        $this->leadAndContractDateReport = $leadAndContractDateReport;
        $this->franchiseRepository = $franchiseRepository;
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

    public function leadAndContract(Request $request)
    {
        $user = Auth::user();

        if($request->has('start_date') && $request->has('end_date')){
            
            $results = [];

            if($user->user_type == User::HEAD_OFFICE){
                if(isset($request->franchise_id)){
                    $franchise = $this->franchiseRepository->findById($request->franchise_id);
                    $franchiseNumber = isset($franchise->franchise_number) ? $franchise->franchise_number : 0;
                    $franchiseIds = $this->franchiseRepository->getFranchiseIds($franchiseNumber);
                } else {
                    $franchiseIds = $this->franchiseRepository->all()->pluck('id')->toArray();
                }

                $results = $this->leadAndContractDateReport->generateLeadAndContract($user->user_type, $franchiseIds, $request->all());
            }else {
                $franchiseIds = $user->franchises->pluck('id')->toArray();
                $results =  $this->leadAndContractDateReport->generateLeadAndContractByFranchise($user->user_type, $franchiseIds, $request->all());
            }

            if(count($results) > 0){
                $total = $this->computeTotal($results);

                return $this->showOne([
                    'results' => $results,
                    'total' => $total //count($results)
                ]);
            }

            return $this->showOne([
                'results' => $results
            ]);
        }
    }
}
