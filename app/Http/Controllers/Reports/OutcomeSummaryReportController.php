<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OutcomeSummaryReportController extends ApiController
{

    protected $reportRepository;

    public function __construct(ReportRepositoryInterface $reportRepository)
    {
        $this->reportRepository = $reportRepository;
        $this->middleware('auth:sanctum');
    }


    public function __invoke(Request $request)
    {
        $user = Auth::user();

        if($request->has('start_date') && $request->has('end_date')){

            $results = [];

            if($user->user_type == User::HEAD_OFFICE){

                $results = $this->reportRepository->generateOutcomeSummary($request->all());

            }else {

                $franchiseIds = $user->franchises->pluck('id')->toArray();

                $results = $this->reportRepository->generateOutcomeSummaryByFranchise($franchiseIds, $request->all());
            }
//            $results = $this->reportRepository->generateOutcomeSummary($request->all());

            return $this->showOne([
                'results' => $results
            ]);
        }
    }
}
