<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Reports\Interfaces\SalesContractVariationReport;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesContractVariationReportController extends ApiController
{
    private $salescontractvariationreport;

    public function __construct(SalesContractVariationReport $salescontractvariationreport)
    {
        $this->middleware('auth:sanctum');
        $this->salescontractvariationreport = $salescontractvariationreport;
    }

    public function __invoke(Request $request)
    {
        $user = Auth::user();

        if($request->has('start_date') && $request->has('end_date')){
            $results = [];

            if($user->user_type == User::HEAD_OFFICE){
                $results = $this->salescontractvariationreport->generate($request->all());
            }else {
                $franchiseIds = $user->franchises->pluck('id')->toArray();
                $results = $this->salescontractvariationreport->generateByFranchise($franchiseIds, $request->all());
            }

            return $this->showOne([
                'results' => $this->formatReport($results)
            ]);
        }
    }

    private function formatReport($results)
    {
        $report = [];

        $set = [];
        $setId = 0;
        $setTotal = 0;

        foreach ($results as $result){
            if($setId == 0) $setId = $result->id;

            if($setId == $result->id){
                array_push($set, $result);
                $setTotal = $setTotal + $result->variation_amount;
            }else {
                //Push the Old Set to the report
                array_push($set, [
                    'name' => 'Total',
                    'variation_amount' => $setTotal
                ]);
                $setId = $result->id;
                array_push($report, $set);

                //Create a new Set
                $set = [];
                array_push($set, $result);
                $setTotal = $result->variation_amount;
            }
        }

        if (count($report) >= 0 && count($set) > 0)
        {
            array_push($set, [
                'name' => 'Total',
                'variation_amount' => $setTotal
            ]);

            array_push($report, $set);
        }
        return $report;
    }
}
