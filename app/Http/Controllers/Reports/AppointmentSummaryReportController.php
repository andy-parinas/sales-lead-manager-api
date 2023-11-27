<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Reports\Interfaces\AppointmentReport;
use Illuminate\Support\Facades\Auth;
use App\User;

class AppointmentSummaryReportController extends ApiController
{
    private $appointmentReport;

    public function __construct(AppointmentReport $appointmentReport)
    {
        $this->middleware('auth:sanctum');
        $this->appointmentReport = $appointmentReport;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        if($request->has('start_date') && $request->has('end_date')){

            $results = [];

            if($user->user_type == User::HEAD_OFFICE){
                $results = $this->appointmentReport->getAllAppointment($request->all());
            }else {

                $franchiseIds = $user->franchises->pluck('id')->toArray();
                $results = $this->appointmentReport->getAllAppointmentByFranchise($franchiseIds, $request->all());
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
                $results = $this->appointmentReport->getAllAppointment($request->all());                
            }else {
                $franchiseIds = $user->franchises->pluck('id')->toArray();
                $results = $this->appointmentReport->getAllAppointmentByFranchise($franchiseIds, $request->all());
            }
            
            //Reports to CSV
            $path = storage_path('app/files/');
            $fileName = 'sales_lead_appointment_report.csv';

            $handle = fopen($path.$fileName, 'w+');
            fputcsv($handle, [
                'Franchise',
                'Design Advisor',
                'Lead Number',
                'Lead Date',
                'Contract Date',
                'Product Name',
                'Quoted Price',
                'Outcome',
                'Comments',
            ]);
            foreach($results as $row) {
                fputcsv($handle, array(
                    $row->franchise,
                    $row->design_advisor,
                    $row->lead_number,
                    $row->lead_date,
                    $row->contract_date,
                    $row->product_name,
                    $row->quoted_price,
                    $row->outcome,
                    $row->comments,
                ));
            }
            fclose($handle);
            $headers = array(
                'Content-Type' => 'text/csv',
            );
            return response()->download($path.$fileName, 'sales_lead_appointment_report.csv', $headers);
        }
    }
}
