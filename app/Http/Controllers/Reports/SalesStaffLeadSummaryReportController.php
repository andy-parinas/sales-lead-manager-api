<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Reports\Interfaces\SalesStaffLeadSummaryReport;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use App\Traits\ReportComputer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesStaffLeadSummaryReportController extends ApiController
{

    use ReportComputer;

    protected $reportRepository;
    protected $salesStaffLeadSummaryReport;

    public function __construct(ReportRepositoryInterface $reportRepository,
    SalesStaffLeadSummaryReport $salesStaffLeadSummaryReport)
    {
        $this->middleware('auth:sanctum');
        $this->reportRepository = $reportRepository;
        $this->salesStaffLeadSummaryReport = $salesStaffLeadSummaryReport;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        if($request->has('start_date') && $request->has('end_date')){

            $results = [];

            if($user->user_type == User::HEAD_OFFICE){
                $results = $this->salesStaffLeadSummaryReport->generate($request->all());
            }else {
                $franchiseIds = $user->franchises->pluck('id')->toArray();
                $results = $this->salesStaffLeadSummaryReport->generateByFranchise($franchiseIds, $request->all());
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
                $results = $this->salesStaffLeadSummaryReport->generate($request->all());
            }else {
                $franchiseIds = $user->franchises->pluck('id')->toArray();
                $results = $this->salesStaffLeadSummaryReport->generateByFranchise($franchiseIds, $request->all());
            }
            
            //$results to csv
            $path = storage_path('app/files/');
            $fileName = 'sales_staff_lead_summary_report.csv';

            $handle = fopen($path.$fileName, 'w+');
            fputcsv($handle, [
                'Design Advisor',
                'Franchise Number',
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
                    $row->numberOfSales,
                    $row->numberOfLeads,
                    $row->totalContracts,
                    $row->conversionRate,
                    $row->averageSalesPrice,
                ));
            }
            
            if($results->count() > 0){
                $total = $this->computeTotal($results);
                fputcsv($handle, [
                    'Total',
                    '',
                    $total['totalNumberOfSales'],
                    $total['totalNumberOfLeads'],
                    $total['grandTotalContracts'],
                    '',
                    '',
                ]);
                fputcsv($handle, [
                    'Average',
                    '',
                    number_format($total['averageNumberOfSales'], 2),
                    number_format($total['averageNumberOfLeads'], 2),
                    number_format($total['averageTotalContract'], 2),
                    $this->percent($total['averageConversionRate']),
                    number_format($total['grandAveragePrice'], 2),
                ]);
            }
            fclose($handle);
            $headers = array(
                'Content-Type' => 'text/csv',
            );
            return response()->download($path.$fileName, 'sales_staff_lead_summary_report.csv', $headers); 

        }
    }

    public function percent($number){
        return $number * 100 . '%';
    }
}
