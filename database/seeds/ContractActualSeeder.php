<?php

use App\Franchise;
use App\Lead;
use Illuminate\Database\Seeder;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class ContractActualSeeder extends Seeder
{

    protected $contractLog;

    public function __construct()
    {
        // Create the logger
        $this->contractLog = new Logger('contract_logger');
        // Now add some handlers
        $this->contractLog->pushHandler(new StreamHandler(storage_path() .'/logs/contract.log', Logger::DEBUG));
        $this->contractLog->pushHandler(new FirePHPHandler());
    }


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $contractFile = storage_path() . '/app/database/contracts.csv';


        $file = fopen($contractFile, 'r');
        $count = 1;
        while (($data = fgetcsv($file)) !== FALSE) {

            $franchiseNumber = trim($data[0]);
            $leadReferenceNumber = trim($data[1]);
            $contractDate = trim($data[2]);
            $contractNumber = trim($data[3]);
            $contractPrice = floatval(trim($data[4]));
            $warrantyRequired = trim($data[5]);
            $deposit = floatval(trim($data[7]));
            $dateDepositReceived = trim($data[8]);
            $dateWarrantySent = trim($data[6]);

            $data = [
                'contract_date' => $contractDate,
                'contract_number' => $contractNumber,
                'contract_price' =>  $contractPrice,
                'deposit_amount' =>  $deposit,
                'date_deposit_received' => $dateDepositReceived != "" ?  $dateDepositReceived : null,
                'total_contract' => $contractPrice,
                'warranty_required' => $warrantyRequired == 1? 'yes' : 'no',
                'date_warranty_sent' => $dateWarrantySent != "" ? $dateWarrantySent : null
            ];

            $franchise = Franchise::where('franchise_number', $franchiseNumber)
                            ->where('parent_id', '<>', null)
                            ->first();

            if($franchise != null){

                $lead = Lead::where('reference_number', $leadReferenceNumber)
                            ->where('franchise_id', $franchise->id)
                            ->first();

                if($lead != null){

                    try{

                        $lead->contract()->create($data);

                    }catch(Exception $exception){
                        
                        if($exception->getCode() == 22007){
                            $this->contractLog->error("[Reference: {$leadReferenceNumber}| Franchise: {$franchiseNumber}] Unable to Create Contracts passibe contract date issue {$contractDate}. No Leads Created");
                        }else {
                            $this->contractLog->error("[Reference: {$leadReferenceNumber}| Franchise: {$franchiseNumber}] Unable to Create Contracts. No Contracts Created");
                        }
                        
                    }

                    print "Contract Created For {$lead->lead_number} Count: {$count} ";
                    
                }else{

                    print "No Lead Found Lead: {$leadReferenceNumber}, FranchiseId: {$franchise->id}, FranchiseNumber: {$franchise->franchise_number} Count: {$count} \n";
                    $this->contractLog->error("[Reference: {$leadReferenceNumber}| Franchise: {$franchiseNumber}] No Lead Found. No Contracts Created");

                }


            }else {
                $this->contractLog->error("[Reference: {$leadReferenceNumber}| Franchise: {$franchiseNumber}] No Franchise Found. No Contracts Created");
            }

            print "\n########## Count Number {$count} ################### \n";
            $count++;

        }


    }
}
