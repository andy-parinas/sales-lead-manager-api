<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\CustomerReviewReportInterface;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerReviewReportController extends ApiController
{

    protected $reportRepository;

    public function __construct(CustomerReviewReportInterface $reportRepository)
    {
        $this->middleware('auth:sanctum');
        $this->reportRepository = $reportRepository;
    }


    public function __invoke(Request $request)
    {
        $user = Auth::user();

        if($request->has('start_date') && $request->has('end_date')){

            $results = [];

            if($user->user_type == User::HEAD_OFFICE){

                $results = $this->reportRepository->getAll($request->all());

            }else {

                $franchiseIds = $user->franchises->pluck('id')->toArray();

                $results = $this->reportRepository->getAllByFranchise($franchiseIds, $request->all());
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
                $results = $this->reportRepository->getAll($request->all());
            }else {
                $franchiseIds = $user->franchises->pluck('id')->toArray();
                $results = $this->reportRepository->getAllByFranchise($franchiseIds, $request->all());
            }

            //$results to csv
            $path = storage_path('app/files/');
            $fileName = 'customer_satisfaction_report.csv';

            $handle = fopen($path.$fileName, 'w+');
            fputcsv($handle, [
                'Project Completion',
                'Last Name & Suburb',
                'Lead Number',
                'Franchise Number',
                'Design Advisor',
                'Product',
                'Service Rating',
                'Workmanship Rating',
                'Product Rating',
                'Design Advisor Rating',
                'Customer Comments',
            ]);
            foreach($results as $row) {
                fputcsv($handle, array(
                    $row->date_project_completed,
                    $row->last_name_suburb,
                    $row->lead_number,
                    $row->franchise_number,
                    $row->first_name.' '.$row->last_name,
                    $row->product_name,
                    $row->service_received_rating,
                    $row->workmanship_rating,
                    $row->finished_product_rating,
                    $row->design_consultant_rating,
                    $row->comments,
                ));
            }
            fclose($handle);
            $headers = array(
                'Content-Type' => 'text/csv',
            );
            return response()->download($path.$fileName, 'customer_satisfaction_report.csv', $headers); 
        }
    }
}
