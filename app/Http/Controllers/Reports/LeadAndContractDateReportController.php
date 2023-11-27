<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Reports\Interfaces\LeadAndContractDateReport;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use App\Repositories\Interfaces\FranchiseRepositoryInterface;
use App\Traits\LeadAndContractDateComputer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadAndContractDateReportController extends ApiController
{
    use LeadAndContractDateComputer;
    protected $reportRepository;
    private $leadAndContractDateReport;
    protected $franchiseRepository;

    public function __construct(
        ReportRepositoryInterface $reportRepository,
        LeadAndContractDateReport $leadAndContractDateReport,
        FranchiseRepositoryInterface $franchiseRepository
    )
    {
        $this->middleware('auth:sanctum');
        $this->reportRepository = $reportRepository;
        $this->leadAndContractDateReport = $leadAndContractDateReport;
        $this->franchiseRepository = $franchiseRepository;
    }


    public function index(Request $request)
    {
        $user = Auth::user();

        if($request->has('start_date') && $request->has('end_date')){
            
            $results = [];

            if($user->user_type == User::HEAD_OFFICE){
                $results = $this->leadAndContractDateReport->generate($request->all());
            }else {
                $franchiseIds = $user->franchises->pluck('id')->toArray();
                $results =  $this->leadAndContractDateReport->generateByFranchise($franchiseIds, $request->all());
            }

            if($results->count() > 0){
                return $this->showOne([
                    'results' => $results,
                    'total' => $results->count()
                ]);
            }

            return $this->showOne([
                'results' => $results
            ]);
        }
    }

    public function leadAndContract(Request $request)
    {
        $user = Auth::user();

        if($request->has('start_date') && $request->has('end_date')){
            
            $results = [];

            if($user->user_type == User::HEAD_OFFICE){
                if(isset($request->franchise_id)){
                    $franchise = $this->franchiseRepository->findById($request->franchise_id);
                    $franchiseNumber = isset($franchise->franchise_number) ? $franchise->franchise_number : 0;
                    $franchiseIds = $this->franchiseRepository->getFranchiseIds($franchiseNumber);
                } else {
                    $franchiseIds = $this->franchiseRepository->all()->pluck('id')->toArray();
                }

                $results = $this->leadAndContractDateReport->generateLeadAndContract($franchiseIds, $request->all());
            }else {
                $franchiseIds = $user->franchises->pluck('id')->toArray();
                $results =  $this->leadAndContractDateReport->generateLeadAndContractByFranchise($franchiseIds, $request->all());
            }

            if(count($results) > 0){
                $total = $this->computeTotal($results);

                return $this->showOne([
                    'results' => $results,
                    'total' => $total //count($results)
                ]);
            }

            return $this->showOne([
                'results' => $results
            ]);
        }
    }

    public function allDesignAdvisory(Request $request)
    {
        $results = $this->leadAndContractDateReport->generateDesignAdvisorById($request->franchise_id);
        //dd($results->toArray());
        return $this->showOne([
            'results' => $results
        ]);
    }

    public function csvReport(Request $request)
    {
        $user = Auth::user();

        if($request->has('start_date') && $request->has('end_date')){
            
            $results = [];

            if($user->user_type == User::HEAD_OFFICE){
                $results = $this->leadAndContractDateReport->generate($request->all());
            }else {
                $franchiseIds = $user->franchises->pluck('id')->toArray();
                $results =  $this->leadAndContractDateReport->generateByFranchise($franchiseIds, $request->all());
            }
            
            //$results to csv
            $filename = 'customer_contact_details_report.csv';
            $handle = fopen($filename, 'w+');
            fputcsv($handle, [
                'Lead Date',
                'Contract Date',
                'First Name',
                'Last Name',
                'Design Advisor',
                'Email Address',
                'Suburb',
                'Postcode',
                'Address',
                'Phone Number',
                'Lead Status',
                'Product Name',
                'Contract Value',
                'How they heard about Spanline',
            ]);
            foreach($results as $row) {
                fputcsv($handle, array(
                    $row->lead_date,
                    $row->contract_date,
                    $row->first_name,
                    $row->last_name,
                    $row->design_advisor,
                    $row->email,
                    $row->suburb,
                    $row->pcode,
                    $row->address,
                    $row->phone_number,
                    $row->lead_status,
                    $row->product_name,
                    $row->contract_price,
                    $row->heard_about,
                ));
            }
            fclose($handle);
            $headers = array(
                'Content-Type' => 'text/csv',
            );
            return response()->download($filename, 'customer_contact_details_report.csv', $headers); 

        }
    }

    public function leadContractCsvReport(Request $request)
    {
        $user = Auth::user();

        if($request->has('start_date') && $request->has('end_date')){
            
            $results = [];

            if($user->user_type == User::HEAD_OFFICE){
                if(isset($request->franchise_id)){
                    $franchise = $this->franchiseRepository->findById($request->franchise_id);
                    $franchiseNumber = isset($franchise->franchise_number) ? $franchise->franchise_number : 0;
                    $franchiseIds = $this->franchiseRepository->getFranchiseIds($franchiseNumber);
                } else {
                    $franchiseIds = $this->franchiseRepository->all()->pluck('id')->toArray();
                }

                $results = $this->leadAndContractDateReport->generateLeadAndContract($franchiseIds, $request->all());
                
            }else {
                $franchiseIds = $user->franchises->pluck('id')->toArray();
                $results =  $this->leadAndContractDateReport->generateLeadAndContractByFranchise($franchiseIds, $request->all());
            }
            
            //$results to csv
            $filename = 'lead_and_contract_date_summary_report.csv';
            $handle = fopen($filename, 'w+');
            fputcsv($handle, [
                'Design Advisor',
                'Franchise',
                '# Leads',
                '# Contracts',
                'Total Contracts',
                'Conversion Rate',
                'Average Sales Price',
            ]);
            foreach($results as $row) {
                fputcsv($handle, array(
                    $row['salesStaff'],
                    $row['franchiseNumber'],
                    $row['totalLeads'],
                    $row['totalContracts'],
                    $row['sumOfTotalContracts'],
                    $row['conversionRate'],
                    $row['averageSalesPrice'],
                ));
            }
            
            if(count($results) > 0){
                $total = $this->computeTotal($results);
                fputcsv($handle, [
                    'Total',
                    '',
                    number_format($total['totalNumberOfLeads'], 2),
                    number_format($total['totalNumberOfContracts'], 2),
                    number_format($total['grandTotalContracts'], 2),
                    '',
                    '',
                ]);
                fputcsv($handle, [
                    'Average',
                    '',
                    '',
                    '',
                    '',
                    number_format($total['averageConversionRate'], 2),
                    number_format($total['grandAveragePrice'], 2),
                ]);
            }
            fclose($handle);
            $headers = array(
                'Content-Type' => 'text/csv',
            );
            return response()->download($filename, 'lead_and_contract_date_summary_report.csv', $headers); 

        }
    }
}
