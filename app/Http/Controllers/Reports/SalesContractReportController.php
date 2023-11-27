<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Reports\Interfaces\SalesContractReport;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesContractReportController extends ApiController
{

    private $salesContractReport;

    public function __construct(SalesContractReport $salesContractReport)
    {
        $this->middleware('auth:sanctum');
        $this->salesContractReport = $salesContractReport;
    }

    public function __invoke(Request $request)
    {
        $user = Auth::user();

        if($request->has('start_date') && $request->has('end_date')){


            $results = [];

            if($user->user_type == User::HEAD_OFFICE){
                $results = $this->salesContractReport->generate($request->all());
            }else {
                $franchiseIds = $user->franchises->pluck('id')->toArray();
                $results = $this->salesContractReport->generateByFranchise($franchiseIds, $request->all());
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
                $setTotal = $setTotal + $result->total_contract;

            }else {
                //Push the Old Set to the report
                array_push($set, [
                    'name' => 'Total',
                    'total_contract' => $setTotal
                ]);
                $setId = $result->id;
                array_push($report, $set);

                //Create a new Set
                $set = [];
                array_push($set, $result);
                $setTotal = $result->total_contract;
            }

        }

        if (count($report) >= 0 && count($set) > 0)
        {
            array_push($set, [
                'name' => 'Total',
                'total_contract' => $setTotal
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
                $results = $this->salesContractReport->generate($request->all());
            }else {
                $franchiseIds = $user->franchises->pluck('id')->toArray();
                $results = $this->salesContractReport->generateByFranchise($franchiseIds, $request->all());
            }
            
            //$results to csv
            $path = storage_path('app/files/');
            $fileName = 'sales_contract_report.csv';

            $handle = fopen($path.$fileName, 'w+');
            fputcsv($handle, [
                'Design Advisor',
                'Contract Date',
                'Lead Number',
                'Customer',
                'Suburb',
                'Product',
                'Lead Source',
                'Roof Sheet Profile',
                'Value',
            ]);
            foreach($results as $row) {
                $roofSheetProfile = isset($row->roof_sheet_profile)? $row->roof_sheet_profile : '';
                fputcsv($handle, array(
                    $row->sales_staff_name,
                    $row->contract_date,
                    $row->lead_number,
                    $row->customer,
                    $row->suburb,
                    $row->product,
                    $row->source,
                    $roofSheetProfile,
                    number_format($row->total_contract, 2),
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
                    number_format($row->total_contract, 2),
                ]);
            }
            fclose($handle);
            $headers = array(
                'Content-Type' => 'text/csv',
            );
            return response()->download($path.$fileName, 'sales_contract_report.csv', $headers);
        }
    }
}
