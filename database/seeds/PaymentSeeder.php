<?php

use App\Franchise;
use App\Lead;
use Illuminate\Database\Seeder;

use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class PaymentSeeder extends Seeder
{


    protected $paymentLog;

    public function __construct()
    {
        // Create the logger
        $this->paymentLog = new Logger('payment_logger');
        // Now add some handlers
        $this->paymentLog->pushHandler(new StreamHandler(storage_path() .'/logs/payment.log', Logger::DEBUG));
        $this->paymentLog->pushHandler(new FirePHPHandler());
    }


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $paymentFile = storage_path() . '/app/database/payments.csv';
        

        $file = fopen($paymentFile, 'r');
        $count = 1;

        while (($data = fgetcsv($file)) !== FALSE) {

            $franchiseNumber = trim($data[0]);
            $leadReferenceNumber = trim($data[1]);
            $paymentDate = trim($data[3]);
            $amount = trim($data[5]);
            $description = trim($data[4]);

            $franchise = Franchise::where('franchise_number', $franchiseNumber)
            ->where('parent_id', '<>', null)
            ->first();

            if($franchise != null){

                $lead = Lead::where('reference_number', $leadReferenceNumber)
                            ->where('franchise_id', $franchise->id)
                            ->first();

                
                if($lead != null){

                    $finance = $lead->finance;

                    if($finance != null){

                        $finance->paymentsMade()->create([
                            'payment_date' => $paymentDate,
                            'description' => $description,
                            'amount' => $amount
                        ]);

                        $totalPayment = $finance->total_payment_made + 1;

                        $finance->update(['total_payment_made' => $totalPayment]);

                        print "Payment Created For {$lead->lead_number} {$finance->id} Count: {$count} \n";
                        $this->paymentLog->info("Contract Created For {$lead->lead_number} Count: {$count} ");
                    }else {
                        print "No Finance Found Found Lead: {$leadReferenceNumber}, FranchiseId: {$franchise->id}, FranchiseNumber: {$franchise->franchise_number} Count: {$count} \n";
                        $this->paymentLog->alert("No Finance Found Lead: {$leadReferenceNumber}, FranchiseId: {$franchise->id}, FranchiseNumber: {$franchise->franchise_number} Count: {$count}");
                    }

                   
                
                }else{

                    print "No Lead Found Lead: {$leadReferenceNumber}, FranchiseId: {$franchise->id}, FranchiseNumber: {$franchise->franchise_number} Count: {$count} \n";
                    $this->paymentLog->alert("No Lead Found Lead: {$leadReferenceNumber}, FranchiseId: {$franchise->id}, FranchiseNumber: {$franchise->franchise_number} Count: {$count}");

                }

            }else {
                $this->paymentLog->alert("No Franchise Found {$franchiseNumber} Count: {$count} ");
            }

            // dd($franchiseNumber,  $leadReferenceNumber, $paymentDate, $amount, $description);

        }

    }
}
