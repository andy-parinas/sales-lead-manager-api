<?php

use App\Postcode;
use Illuminate\Database\Seeder;

class PostcodeTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $leadFile = storage_path() . '/app/database/leads.csv';

        $file = fopen($leadFile, 'r');
        $count = 1;
        while (($data = fgetcsv($file)) !== FALSE) {

            $pcode = trim($data[10]);
            $locality = trim($data[8]);
            $state = trim($data[9]);
            $referenceNumber =  trim($data[1]);

            $postcode = $this->getPostcode($pcode, $locality, $state, $count, $referenceNumber );

            dump($postcode != null? $postcode->pcode : "Null");

            sleep(2);
        }
    }



    private function getPostcode($pcode, $locality, $state, $reference){


        $postcode = null;


        // Check if Postcode is present and locality is present
        // Ideal Scenario ot get the accurate postcode
        if($pcode != "" && $locality != ""){

            dump("Postcode {$pcode} and Locality {$locality} Data Available");

            $postcode = Postcode::where('pcode',$pcode )
                ->where('locality', strtoupper($locality) )->first();

            

            if($postcode != null) {
                dump("Postcode {$pcode} and Locality {$locality} valid returning");
                return $postcode;
            }

        }elseif($pcode != ""){ // Assume Locality has no value

            dump("Postcode {$pcode} Avaialble | Locality {$locality} Missing");


            $postcode = Postcode::where('pcode',$pcode )->first();

            if($postcode != null) {
                dump("Postcode {$pcode} valid returning");
                return $postcode;
            }


        }elseif($locality != ""){ //Assume pcode has no value

            dump("Postcode {$pcode} Missing | Locality {$locality} Available");


            if($state != "" && $this->checkState($state)){

                dump("Locality {$locality} Available and State {$state} Available");

                $postcode = Postcode::where('locality', $locality)
                    ->where('state', strtoupper($state) )->first();

                if($postcode != null) {
                    dump("Locality {$locality} Available and State {$state} Valid returning");
                    return $postcode;
                }

            }else {


                dump("Locality {$locality} Available and State {$state} Not Valid");

                $postcode = Postcode::where('locality', $locality)->first();

                if($postcode != null) {
                    dump("defaulted to postcode {$postcode->pcode} of Locality: {$locality} returning");
                    return $postcode;
                }
            }

        }elseif($state != "" && $this->checkState($state)){

            dump("Only State {$state} is Valid");


            $postcode = Postcode::where('state', strtoupper($state))->first();

            if($postcode != null) {
                dump(" defaulted to first postcode {$postcode->pcode} of State: {$state} returning");
                return $postcode;
            }
        }
        
        
        if($pcode != "" && $locality != "" && $state != "" && $this->checkState($state)) {

            dump("No Valid postcode data can be found. But Can Create a postocde. Will create postcode entry");
            dump("Postcode: {$pcode} | Locality {$locality} | State {$state}");


            // $postcode = Postcode::where('state', strtoupper($state))
            //     ->where('locality',  strtoupper($locality))
            //     ->first();

            // if($postcode != null) {
            //     $this->postCodeLogger->info("Reference: {$reference} Created a postcode entry {$postcode->pcode} State: {$state} Locality: {$locality}");
            //     return $postcode;
            // }

            return null;
        }

        // IF cannot find or cannot create a postcode due to incorect or missing data
        // Get the first postcode in the database
        if($postcode == null){

            dump("No Valid postcode data can be found. Can not Create Postcode. Defaulting to First Postcode");


            $postcode = Postcode::first();
            // $this->postCodeLogger->info("Reference: {$reference} Missing and Incorect data. Getting the first postcode in the database. {$postcode->pcode} State: {$postcode->state} Locality: {$postcode->locality}");

        }

        return $postcode;

    }


    private function checkState($state)
    {
        return in_array(strtoupper($state), ['ACT', 'NT', 'SA', 'WA', 'NSW', 'VIC', 'QLD', 'TAS']);
    }
}
