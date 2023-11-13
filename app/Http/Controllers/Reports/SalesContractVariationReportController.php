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

    public function csvReport(Request $request)
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
            
            //$results to csv
            $filename = 'sales_contract_variation_report.csv';
            $handle = fopen($filename, 'w+');
            fputcsv($handle, [
                'Design Advisor',
                'Variation Date',
                'Contract Date',
                'Lead Number',
                'Customer',
                'Suburb',
                'Product',
                'Lead Source',
                'value',
            ]);
            foreach($results as $row) {
                fputcsv($handle, array(
                    $row->sales_staff_name,
                    date('d/m/Y', strtotime($row->variation_date)),
                    date('d/m/Y', strtotime($row->contract_date)),
                    $row->lead_number,
                    $row->customer,
                    $row->suburb,
                    $row->product,
                    $row->source,
                    number_format($row->variation_amount, 2),
                ));
                fputcsv($handle, [
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    'Total',
                    number_format($row->variation_amount, 2),
                ]);
            }
            fclose($handle);
            $headers = array(
                'Content-Type' => 'text/csv',
            );
            return response()->download($filename, 'sales_contract_variation_report.csv', $headers);
        }
    }
}
