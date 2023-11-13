<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Reports\Interfaces\ProductSalesSummaryReport;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use App\Traits\ReportComputer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductSalesSummaryReportController extends ApiController
{

    use ReportComputer;

    protected $reportRepository;
    protected $productSalesSummaryReport;

    public function __construct(ReportRepositoryInterface $reportRepository,
                                ProductSalesSummaryReport $productSalesSummaryReport)
    {
        $this->middleware('auth:sanctum');
        $this->reportRepository = $reportRepository;
        $this->productSalesSummaryReport = $productSalesSummaryReport;
    }


    public function index(Request $request)
    {
        $user = Auth::user();

        if($request->has('start_date') && $request->has('end_date')) {

            $results = [];
            
            if($user->user_type == User::HEAD_OFFICE){
                $results = $this->productSalesSummaryReport->generate($request->all());
            }else {
                $franchiseIds = $user->franchises->pluck('id')->toArray();
                $results = $this->productSalesSummaryReport->generateByFranchise($franchiseIds, $request->all());
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
                $results = $this->productSalesSummaryReport->generate($request->all());
            }else {
                $franchiseIds = $user->franchises->pluck('id')->toArray();
                $results = $this->productSalesSummaryReport->generateByFranchise($franchiseIds, $request->all());
            }
            
            //$results to csv
            $filename = 'franchise_product_sales_summary_report.csv';
            $handle = fopen($filename, 'w+');
            fputcsv($handle, [
                'Product',
                '# Sales',
                '# Leads',
                'Total Contracts',
                'Conversion Rate',
                'Average Sales Price',
            ]);
            foreach($results as $row) {
                fputcsv($handle, array(
                    $row->name,
                    $row->numberOfSales,
                    $row->numberOfLeads,                    
                    number_format($row->totalContracts, 2),
                    $this->percent($row->conversionRate),
                    number_format($row->averageSalesPrice, 2),
                ));
            }
            
            if($results->count() > 0){
                $total = $this->computeTotal($results);
                fputcsv($handle, [
                    'Total',
                    number_format($total['totalNumberOfSales'], 2),
                    number_format($total['totalNumberOfLeads'], 2),
                    number_format($total['grandTotalContracts'], 2),
                    '',
                    '',
                ]);
                fputcsv($handle, [
                    'Average',
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
            return response()->download($filename, 'franchise_product_sales_summary_report.csv', $headers);
        }
    }

    public function percent($number){
        return $number * 100 . '%';
    }
}
