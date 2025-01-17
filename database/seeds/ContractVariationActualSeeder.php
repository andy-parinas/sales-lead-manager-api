<?php

use App\Franchise;
use App\Lead;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class ContractVariationActualSeeder extends Seeder
{

    protected $variationLog;

    public function __construct()
    {
        // Create the logger
        $this->variationLog = new Logger('variation_logger');
        // Now add some handlers
        $this->variationLog->pushHandler(new StreamHandler(storage_path() .'/logs/variation.log', Logger::DEBUG));
        $this->variationLog->pushHandler(new FirePHPHandler());
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $contractFile = storage_path() . '/app/database/contracts_variation.csv';


        $file = fopen($contractFile, 'r');
        $count = 1;
        while (($data = fgetcsv($file)) !== FALSE) {

            $variationDate = trim($data[4]) != "" ? trim($data[4])  : date("Y-m-d");
            $description = trim($data[5]);
            $amount = floatval(trim($data[6]));

            $franchiseNumber = trim($data[0]);
            $leadReferenceNumber = trim($data[1]);

            $franchise = Franchise::where('franchise_number', $franchiseNumber)
                ->where('parent_id', '<>', null)
                ->first();

            if($franchise != null){

                $lead = Lead::where('reference_number', $leadReferenceNumber)
                    ->where('franchise_id', $franchise->id)
                    ->first();


                if($lead != null) {

                    $contract = $lead->contract;

                    if($contract != null){

                            $data = [
                                'variation_date' => $variationDate,
                                'description' => $description,
                                'amount' => $amount,
                            ];

                            try {

                                $variation = $contract->contractVariations()->create($data);

                                $total_variation = $contract->total_variation + $variation->amount;
                                $total_contract = $contract->total_contract + $variation->amount;
    
                                $contract->update([
                                    'total_variation' => $total_variation,
                                    'total_contract' => $total_contract
                                ]);
    
                                print "Variation Created and Contract Updated For {$contract->id} \n";
                                $this->variationLog->info("Variation Created and Contract Updated For {$contract->id}");


                            }catch(Exception $exception){

                                if($exception->getCode() == 22007){
                                    $this->variationLog->error("[Reference: {$leadReferenceNumber}| Franchise: {$franchiseNumber}] Unable to Create Contracts Variation passibe contract date issue {$variationDate}. No Leads Created");
                                }else {
                                    $this->contractLog->error("[Reference: {$leadReferenceNumber}| Franchise: {$franchiseNumber}] Unable to Create Contracts. No Contracts Created");
                                }

                            }

                           

                    }else {

                        $this->variationLog->error("[Reference: {$leadReferenceNumber}| Franchise: {$franchiseNumber}] No Contract Found. No Contract variation created");
                    }

                }else {
                    $this->variationLog->error("[Reference: {$leadReferenceNumber}| Franchise: {$franchiseNumber}]  No Lead Found. No Contract variation created");
                }

            }else {
                $this->variationLog->error("[Reference: {$leadReferenceNumber}| Franchise: {$franchiseNumber}] No Franchise Found. No Contract variation created");
            }

            print "\n########## Count Number {$count} ################### \n";
            $count++;

        }
    }
}
