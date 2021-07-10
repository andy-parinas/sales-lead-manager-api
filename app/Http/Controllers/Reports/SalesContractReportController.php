<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Reports\Interfaces\SalesContractReport;
use Illuminate\Http\Request;

class SalesContractReportController extends Controller
{

    private $salesContractReport;

    public function __construct(SalesContractReport $salesContractReport)
    {
        $this->salesContractReport = $salesContractReport;
    }

    public function __invoke(Request $request)
    {


        $results = $this->salesContractReport->generate($request->all());


        return $this->formatReport($results);


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
                $setTotal = $setTotal + $result->total_contract;

            }else {
                //Push the Old Set to the report
                array_push($set, ['setTotal' => $setTotal]);
                $setId = $result->id;
                array_push($report, $set);

                //Create a new Set
                $set = [];
                array_push($set, $result);
                $setTotal = $result->total_contract;
            }

        }

        return $report;


    }
}
