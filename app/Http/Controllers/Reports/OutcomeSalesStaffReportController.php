<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OutcomeSalesStaffReportController extends ApiController
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

                $results = $this->reportRepository->generateOutcomeSalesStaff($request->all());

            }else {

                $franchiseIds = $user->franchises->pluck('id')->toArray();

                $results = $this->reportRepository->generateOutcomeSalesStaffByFranchise($franchiseIds, $request->all());
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
                $results = $this->reportRepository->generateOutcomeSalesStaff($request->all());
            }else {
                $franchiseIds = $user->franchises->pluck('id')->toArray();
                $results = $this->reportRepository->generateOutcomeSalesStaffByFranchise($franchiseIds, $request->all());
            }

            //$results to csv
            $path = storage_path('app/files/');
            $fileName = 'outcome_summary_report.csv';

            $handle = fopen($path.$fileName, 'w+');
            fputcsv($handle, [
                'Franchise',
                'Sales Staff',
                'Outcome',
                'Number Of Leads',
            ]);
            foreach($results as $row) {
                fputcsv($handle, array(
                    $row->franchise_number,
                    $row->salesStaff,
                    $row->outcome,
                    $row->numberOfLeads,
                ));
            }
            fclose($handle);
            $headers = array(
                'Content-Type' => 'text/csv',
            );
            return response()->download($path.$fileName, 'outcome_summary_report.csv', $headers); 

        }
    }
}
