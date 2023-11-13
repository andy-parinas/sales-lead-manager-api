<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Reports\Interfaces\RoofSheetProfileReport;
use App\Traits\RoofSheetProfileComputer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoofSheetProfileReportController extends ApiController
{
    use RoofSheetProfileComputer;

    private $roofSheetProfileReport;

    public function __construct(RoofSheetProfileReport $roofSheetProfileReport)
    {
        $this->middleware('auth:sanctum');
        $this->roofSheetProfileReport = $roofSheetProfileReport;
    }

    public function __invoke(Request $request)
    {
        $user = Auth::user();
        
        if($request->has('start_date') && $request->has('end_date')){

            $results = [];
            
            if($user->user_type == User::HEAD_OFFICE){
                $results = $this->roofSheetProfileReport->generate($request->all());
            }else {
                $franchiseIds = $user->franchises->pluck('id')->toArray();
                $results = $this->roofSheetProfileReport->generateByFranchise($franchiseIds, $request->all());
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

    public function csvReport(Request $request)
    {
        $user = Auth::user();

        if($request->has('start_date') && $request->has('end_date')){
            
            $results = [];

            if($user->user_type == User::HEAD_OFFICE){
                $results = $this->roofSheetProfileReport->generate($request->all());
            }else {
                $franchiseIds = $user->franchises->pluck('id')->toArray();
                $results = $this->roofSheetProfileReport->generateByFranchise($franchiseIds, $request->all());
            }
            
            //$results to csv
            $filename = 'roof_sheet_profile_report.csv';
            $handle = fopen($filename, 'w+');
            fputcsv($handle, [
                'Roof Sheet Profile',
                'Design Advisor',
                'Franchise',
                '# Sales',
                'Total Value of Sales',
            ]);
            foreach($results as $row) {
                fputcsv($handle, array(
                    $row->roof_sheet_profile,
                    $row->salesStaff,
                    $row->franchise,
                    $row->numberOfSales,
                    $row->valueOfSales,
                ));
            }

            if($results->count() > 0){
                $total = $this->computeTotal($results);
                fputcsv($handle, [
                    'Total',
                    '',
                    '',
                    number_format($total['totalNumberOfSales'], 2),
                    number_format($total['grandTotalContractPrice'], 2),
                    
                ]);
            }
            fclose($handle);
            $headers = array(
                'Content-Type' => 'text/csv',
            );
            return response()->download($filename, 'roof_sheet_profile_report.csv', $headers);
        }
    }
}
