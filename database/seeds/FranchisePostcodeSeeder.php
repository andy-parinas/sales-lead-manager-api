<?php

use App\Franchise;
use App\Postcode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Monolog\Logger;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;

class FranchisePostcodeSeeder extends Seeder
{

    protected $postCodeLogger;


    public function __construct()
    {
        $this->postCodeLogger = new Logger('postcode_logger');
        $this->postCodeLogger->pushHandler(new StreamHandler(storage_path() .'/logs/franchise_postcode.log', Logger::DEBUG));
        $this->postCodeLogger->pushHandler(new FirePHPHandler());
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $postcodeFranchiseFile = storage_path() . '/app/database/franchise_postcode.csv';

        $file = fopen($postcodeFranchiseFile, 'r');

        while (($data = fgetcsv($file)) !== FALSE) {
            $pcode = trim($data[0]);
            $locality = trim($data[1]);
            $state = trim($data[2]);
            $franchiseNumber = trim($data[3]);

            $postcode = Postcode::where('pcode', $pcode)
            ->where('locality', $locality)
            ->first();


            if($postcode != null){

                $subFranchise =  Franchise::where('franchise_number', $franchiseNumber)
                ->where('parent_id', '<>', null)->first();

                if($subFranchise != null){
                    $mainFranchise = $subFranchise->parent;

                    if($mainFranchise != null){

                        $subFranchise->postcodes()->attach($postcode->id);
                        $mainFranchise->postcodes()->attach($postcode->id);
        
                        print "Postcode: " . $postcode->pcode . " PostcodeId: " . $postcode->id .
                            " | Sub-Franchise: " . $subFranchise->franchise_number . " Sub-FranchiseId: ". $subFranchise->id . "\n";
        
                        print "Postcode: " . $postcode->pcode . " PostcodeId: " . $postcode->id .
                            " | Main-Franchise: " . $mainFranchise->franchise_number . " Main-FranchiseId: ". $mainFranchise->id . "\n";

                    }else {
                        print "Main Franchise is Missing for : {$franchiseNumber} {$subFranchise->id}\n";
                        $this->postCodeLogger->alert("Main Franchise is Missing for : {$franchiseNumber} {$subFranchise->id}");
                    }

                }else {
                    print "SubFranchise is Missing: {$franchiseNumber}\n";
                    $this->postCodeLogger->alert("SubFranchise is Missing: {$franchiseNumber}");
                }

            }else {
                print "Postcode is Missing Postcode: {$pcode} Locality: {$locality} State: {$state} \n";
                $this->postCodeLogger->alert("Postcode is Missing Postcode: {$pcode} Locality: {$locality} State: {$state}");
            }

        }



    }
}
