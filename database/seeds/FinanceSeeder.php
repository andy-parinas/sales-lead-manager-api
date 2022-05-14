<?php

use App\Franchise;
use App\Lead;
use Illuminate\Database\Seeder;

use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;


class FinanceSeeder extends Seeder
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
        
        $paymentFile = storage_path() . '/app/database/finance.csv';
        

        $file = fopen($paymentFile, 'r');
        $count = 1;

        while (($data = fgetcsv($file)) !== FALSE) {

            $franchiseNumber = trim($data[0]);
            $leadReferenceNumber = trim($data[1]);
            $projecPrice = trim($data[2]);
            $gst = trim($data[3]);
            $contractPrice = trim($data[4]);
            $deposit = trim($data[5]);
            $balance = trim($data[6]);
            $totalPaymentMade = 0;

            $franchise = Franchise::where('franchise_number', $franchiseNumber)
            ->where('parent_id', '<>', null)
            ->first();


            if($franchise != null){

                $lead = Lead::where('reference_number', $leadReferenceNumber)
                            ->where('franchise_id', $franchise->id)
                            ->first();


                if($lead != null){

                    $contract = $lead->contract;

                    if($contract != null){

                        $lead->finance()->create([
                            'project_price' => $projecPrice,
                            'gst' => $gst,
                            'contract_price' => $contractPrice,
                            'total_contract' => $contract->total_contract,
                            'deposit' => $deposit,
                            'balance' => $balance,
                            'total_payment_made' => $totalPaymentMade
                        ]);

                        print "Finance Created Lead: {$leadReferenceNumber}, FranchiseId: {$franchise->id}, FranchiseNumber: {$franchise->franchise_number} Count: {$count} \n";

                    }else {

                        print "No Contract Found Lead: {$leadReferenceNumber}, FranchiseId: {$franchise->id}, FranchiseNumber: {$franchise->franchise_number} Count: {$count} \n";
                        $this->logger->alert("No Lead Found Lead: {$leadReferenceNumber}, FranchiseId: {$franchise->id}, FranchiseNumber: {$franchise->franchise_number} Count: {$count}");
    
                    }


                }else{

                    print "No Lead Found Lead: {$leadReferenceNumber}, FranchiseId: {$franchise->id}, FranchiseNumber: {$franchise->franchise_number} Count: {$count} \n";
                    $this->logger->alert("No Lead Found Lead: {$leadReferenceNumber}, FranchiseId: {$franchise->id}, FranchiseNumber: {$franchise->franchise_number} Count: {$count}");

                }

                

            }else {
                $this->logger->alert("No Franchise Found {$franchiseNumber} Count: {$count} ");
            }

        }


    }
}
