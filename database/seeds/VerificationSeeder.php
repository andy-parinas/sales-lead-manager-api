<?php

use Illuminate\Database\Seeder;
use App\Franchise;
use App\Lead;
use App\RoofColour;
use App\RoofSheet;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class VerificationSeeder extends Seeder
{


    protected $logger;

    public function __construct()
    {
        // Create the logger
        $this->logger = new Logger('payment_logger');
        // Now add some handlers
        $this->logger->pushHandler(new StreamHandler(storage_path() .'/logs/payment.log', Logger::DEBUG));
        $this->logger->pushHandler(new FirePHPHandler());
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csvFile = storage_path() . '/app/database/verification.csv';
        

        $file = fopen($csvFile, 'r');
        $count = 1;

        while (($data = fgetcsv($file)) !== FALSE) {

            $franchiseNumber = trim($data[0]);
            $leadReferenceNumber = trim($data[1]);
            $designCorrect = (int)trim($data[2]) == 1? 'yes' : 'no';
            $dateDesignCheck = trim($data[3])  != "" ? trim($data[3]) : null;
            $costingCorrect = (int)trim($data[4]) == 1? 'yes' : 'no';
            $dateCostingCheck = trim($data[5])  != "" ? trim($data[5]) : null;
            $estimatedBuildDays = (int)trim($data[6]);
            $tradesRequired = trim($data[7]);
            $buildingSupervisor = trim($data[8]);
            $roofSheetName = trim($data[9]);
            $roofColourName = trim($data[10]);
            $linealMetres = trim($data[11]);
            $franchiseAuthority = (int)trim($data[12]) == 1? 'yes' : 'no';
            $authorityDate = trim($data[13]) != "" ? trim($data[13]) : null;


            $franchise = Franchise::where('franchise_number', $franchiseNumber)
            ->where('parent_id', '<>', null)
            ->first();

            if($franchise != null){

                $lead = Lead::where('reference_number', $leadReferenceNumber)
                ->where('franchise_id', $franchise->id)
                ->first();


                if($lead != null){

                    $roofColour = RoofColour::where('name', $roofColourName)->first();
                    $roofSheet = RoofSheet::where('name', $roofSheetName)->first();

                    $lead->verification()->create([

                        'design_correct' => $designCorrect,
                        'date_design_check' => $dateDesignCheck,
                        'costing_correct' => $costingCorrect,
                        'date_costing_check' => $dateCostingCheck,
                        'estimated_build_days' => $estimatedBuildDays,
                        'trades_required' => $tradesRequired,
                        'building_supervisor' => $buildingSupervisor,
                        'roof_sheet_id' => $roofSheet != null ? $roofSheet->id : null,
                        'roof_colour_id' => $roofColour != null ? $roofColour->id : null,
                        'lineal_metres' => $linealMetres,
                        'franchise_authority' => $franchiseAuthority,
                        'authority_date' => $authorityDate,

                    ]);

                    print "Verification Created Lead: {$leadReferenceNumber}, FranchiseId: {$franchise->id}, FranchiseNumber: {$franchise->franchise_number} Count: {$count} \n";


                }else {
                    print "No Lead Found Lead: {$leadReferenceNumber}, FranchiseId: {$franchise->id}, FranchiseNumber: {$franchise->franchise_number} Count: {$count} \n";
                    $this->logger->alert("No Lead Found Lead: {$leadReferenceNumber}, FranchiseId: {$franchise->id}, FranchiseNumber: {$franchise->franchise_number} Count: {$count}");    
                }

            }else {
                $this->logger->alert("No Franchise Found {$franchiseNumber} Count: {$count} ");
            }



        }
    }
}
