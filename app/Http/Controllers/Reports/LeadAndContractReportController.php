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
        $this->middleware('auth:sanctum');
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

    public function csvReport(Request $request)
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
            $formatedResults = $this->formatReport($results);

            //$formatedResults to csv
            $fileName = 'sales_lead_report.csv';
            $headers = array(
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            );

            //delete first column
            $columns = array(
                '',
            );
            
            $callback = function() use ($formatedResults, $columns)
            {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach($formatedResults as $row) {
                    //$row['salesStaff'] in 1 row
                    fputcsv($file, array(
                        $row['salesStaff'],
                    ));

                    //add columns
                    fputcsv($file, array(
                        'Lead Date',
                        'Lead Number',
                        'Contact',
                        'Suburb',
                        'Product',
                        'Source',
                        'Outcome',
                        'Quoted Price',
                        'Contract Price',
                    ));
                    
                    $summary = $row['summary'];
                    $data = $row['data'];

                    foreach($data as $row) {
                        $contractPrice = ($row['contractPrice'] > 0) ? $row['contractPrice'] : 0;
                        fputcsv($file, array(
                            $row['leadDate'],
                            $row['leadNumber'],
                            $row['contact'],
                            $row['suburb'],
                            $row['product'],                            
                            $row['source'],
                            $row['outcome'],
                            $row['quotedPrice'],
                            $contractPrice,
                        ));
                    }

                    fputcsv($file, array(
                        'Leads Seen',
                        $summary['leadsSeen'],
                    ));

                    fputcsv($file, array(
                        'Leads Won',
                        $summary['leadsWon'],
                    ));

                    fputcsv($file, array(
                        'Sales Total',
                        number_format($summary['salesTotal'], 2),
                        '',
                    ));

                    fputcsv($file, array(
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                    ));
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
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
                // dump("Same Id", $result->id);

                if (!array_key_exists("salesStaff", $set)){
                    $set['salesStaff'] = $result->sales_staff;
                }
                
                $data = $this->formatData($result);
                array_push($setData, $data);
                
                $leadsSeen = $leadsSeen + 1;
                if ($result->total_contract != null || $result->total_contract > 0) $leadsWon = $leadsWon +1;
                if ($result->total_contract != null || $result->total_contract > 0) $salesTotal = $salesTotal + $result->total_contract;
            }else {
                // WHen the SalesStaff ID changed Push it here.
                //Push the setData
                $set['data'] = $setData;
                $set['summary'] = [
                    "leadsSeen" => $leadsSeen,
                    "leadsWon" => $leadsWon,
                    "salesTotal" => $salesTotal
                ];

                $setId = $result->id;
                array_push($report, $set); // Push the Set to the Report

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

        if (count($report) >= 0 && count($set) > 0){
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
            'contractPrice' => $result->total_contract,
            'suburb' =>  $result->suburb,
            'postcode' => $result->postcode,
            'contact' => $result->last_name
        ];
    }

    
}
