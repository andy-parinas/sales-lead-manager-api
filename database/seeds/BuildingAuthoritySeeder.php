<?php

use App\BuildingAuthority;
use App\Franchise;
use App\Lead;
use Illuminate\Database\Seeder;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class BuildingAuthoritySeeder extends Seeder
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
          
        $csvFile = storage_path() . '/app/database/building_authority.csv';
        

        $file = fopen($csvFile, 'r');
        $count = 1;

        while (($data = fgetcsv($file)) !== FALSE) {

            $franchiseNumber = trim($data[0]);
            $leadReferenceNumber = trim($data[1]);
            $approvalRequired = (int)trim($data[2]) == 1 ? 'yes' : 'no';
            $buidlingAuthorityName = trim($data[3]);
            $datePlansSentToDraftsman = trim($data[4]) != "" ? trim($data[4]) : null;
            $datePlansCompleted = trim($data[5]) != "" ? trim($data[5]) : null;
            $datePlansSentToAuthority = trim($data[6]) != "" ? trim($data[6]) : null;
            $buildingInsuranceName = trim($data[7]);
            $buildingInsuranceNumber = trim($data[8]);
            $dateInsuranceRequestSent = trim($data[9]) != "" ? trim($data[9]) : null;
            $dateAnticiaptedApproval = trim($data[10]) != "" ? trim($data[10]) : null;
            $dateReceiveFromAuthority = trim($data[11]) != "" ? trim($data[11]) : null;
            $permitNumber = trim($data[12]);
            $securityDepositRequired = trim($data[13]);
            $buildingAuthorityComments = trim($data[14]);


            $franchise = Franchise::where('franchise_number', $franchiseNumber)
            ->where('parent_id', '<>', null)
            ->first();

            if($franchise != null){

                $lead = Lead::where('reference_number', $leadReferenceNumber)
                ->where('franchise_id', $franchise->id)
                ->first();


                if($lead != null){

                    $lead->buildingAuthority()->create([

                        'approval_required' => $approvalRequired,
                        'building_authority_name' => $buidlingAuthorityName,
                        'date_plans_sent_to_draftsman' => $datePlansSentToDraftsman,
                        'date_plans_completed' => $datePlansCompleted,
                        'date_plans_sent_to_authority' => $datePlansSentToAuthority,
                        'building_authority_comments' => $buildingAuthorityComments,
                        'date_anticipated_approval' => $dateAnticiaptedApproval,
                        'date_received_from_authority' => $dateReceiveFromAuthority,
                        'permit_number' => $permitNumber,
                        'security_deposit_required' => $securityDepositRequired,
                        'building_insurance_name' => $buildingInsuranceName,
                        'building_insurance_number' => $buildingInsuranceNumber,
                        'date_insurance_request_sent' => $dateInsuranceRequestSent,

                    ]);

                    print "Building Authority Created Lead: {$leadReferenceNumber}, FranchiseId: {$franchise->id}, FranchiseNumber: {$franchise->franchise_number} Count: {$count} \n";


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
