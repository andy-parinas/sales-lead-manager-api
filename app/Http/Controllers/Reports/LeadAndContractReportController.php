<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Reports\Interfaces\LeadAndContractReport;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadAndContractReportController extends ApiController
{
    private $leadAndContractReport;

    public function __construct(LeadAndContractReport $leadAndContractReport)
    {
//        $this->middleware('auth:sanctum');
        $this->leadAndContractReport = $leadAndContractReport;
    }


    public function __invoke(Request $request)
    {


        $user = Auth::user();

        if($request->has('start_date') && $request->has('end_date')){


            $results = [];

            if($user->user_type == User::HEAD_OFFICE){

                $results = $this->leadAndContractReport->generate($request->all());

            }else {

                $franchiseIds = $user->franchises->pluck('id')->toArray();
                $results =  $this->leadAndContractReport->generateByFranchise($franchiseIds, $request->all());

            }


            return $this->showOne($this->formatReport($results));

        }



    }

    private function formatReport($results)
    {

        $report = [];
        $set = [];
        $setData = [];
        $setId = 0;

        $leadsSeen = 0;
        $leadsWon = 0;
        $salesTotal = 0;

        foreach ($results as $result)
        {
            if($setId == 0) {
                $setId = $result->id;
            }

            if($setId == $result->id){

                if (!array_key_exists("salesStaff", $set))
                {
                    $set['salesStaff'] = $result->sales_staff;
                }


                $data = $this->formatData($result);

                array_push($setData, $data);

                $leadsSeen = $leadsSeen + 1;
                if ($result->total_contract != null || $result->total_contract > 0) $leadsWon = $leadsWon +1;
                if ($result->total_contract != null || $result->total_contract > 0) $salesTotal = $salesTotal + $result->total_contract;


            }else {

                //Push the setData
                $set['data'] = $setData;
                $set['summary'] = [
                    "leadsSeen" => $leadsSeen,
                    "leadsWon" => $leadsWon,
                    "salesTotal" => $salesTotal
                ];

                $setId = $result->id;
                array_push($report, $set);

                //Reset and Feed
                $set = [];
                $setData = [];
                $leadsSeen = 1;
                $salesTotal = 0;

                if ($result->total_contract != null || $result->total_contract > 0) {
                    $salesTotal = $salesTotal + $result->total_contract;
                }else {
                    $salesTotal = 0;
                }

                if ($result->total_contract != null || $result->total_contract > 0){
                    $leadsWon = 1;
                }else {
                    $leadsWon = 0;
                }

                $set['salesStaff'] = $result->sales_staff;
                $data = $this->formatData($result);
                array_push($setData, $data);

            }

        }

        if (count($report) == 0 && count($set) > 0)
        {
            $set['data'] = $setData;
            $set['summary'] = [
                "leadsSeen" => $leadsSeen,
                "leadsWon" => $leadsWon,
                "salesTotal" => $salesTotal
            ];

            array_push($report, $set);
        }

        return $report;

    }

    private function formatData($result)
    {
        return [
            'leadDate' => $result->lead_date,
            'leadNumber' => $result->lead_number,
            'product' => $result->product_name,
            'source' => $result->lead_source,
            'outcome' => $result->outcome,
            'quotedPrice' => $result->quoted_price,
            'contractPrice' => $result->total_contract
        ];
    }
}
