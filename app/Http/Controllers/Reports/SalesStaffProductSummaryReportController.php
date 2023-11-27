<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Reports\Interfaces\SalesStaffProductReport;
use App\Reports\Interfaces\SalesStaffSummaryReport;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use App\Traits\ReportComputer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesStaffProductSummaryReportController extends ApiController
{
    use ReportComputer;

    protected $reportRepository;
    protected $salesStaffProductReport;

    public function __construct(
        ReportRepositoryInterface $reportRepository,
        SalesStaffProductReport $salesStaffProductReport)
    {
        $this->middleware('auth:sanctum');
        $this->reportRepository = $reportRepository;
        $this->salesStaffProductReport = $salesStaffProductReport;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        if($request->has('start_date') && $request->has('end_date')){

            $results = [];

            if($user->user_type == User::HEAD_OFFICE){
                $results = $this->salesStaffProductReport->generate($request->all());
            }else {
                $franchiseIds = $user->franchises->pluck('id')->toArray();
                $results = $this->salesStaffProductReport->generateByFranchise($franchiseIds, $request->all());
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
                $results = $this->salesStaffProductReport->generate($request->all());
            }else {
                $franchiseIds = $user->franchises->pluck('id')->toArray();
                $results = $this->salesStaffProductReport->generateByFranchise($franchiseIds, $request->all());
            }
            
            //$results to csv
            $path = storage_path('app/files/');
            $fileName = 'sales_staff_product_report.csv';

            $handle = fopen($path.$fileName, 'w+');
            fputcsv($handle, [
                'Design Advisor',
                'Franchise',
                'Product Name',
                '# Sales',
                '# Leads',
                'Total Contracts',
                'Conversion Rate',
                'Average Sales Price',
            ]);
            foreach($results as $row) {
                fputcsv($handle, array(
                    $row->salesStaff,
                    $row->franchiseNumber,
                    $row->productName,
                    $row->numberOfSales,
                    $row->numberOfLeads,
                    number_format($row->totalContracts, 2),
                    $row->conversionRate.'%',
                    number_format($row->averageSalesPrice, 2),
                ));
            }
            
            if($results->count() > 0){
                $total = $this->computeTotal($results);
                fputcsv($handle, [
                    'Total',
                    '',
                    '',
                    number_format($total['totalNumberOfSales'], 2),
                    number_format($total['totalNumberOfLeads'], 2),
                    number_format($total['grandTotalContracts'], 2),
                    '',
                    '',
                ]);
            }
            fclose($handle);
            $headers = array(
                'Content-Type' => 'text/csv',
            );
            return response()->download($path.$fileName, 'sales_staff_product_report.csv', $headers); 

        }
    }

    public function percent($number){
        return $number * 100 . '%';
    }
}
