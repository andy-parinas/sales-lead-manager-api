<?php

use App\Appointment;
use App\Franchise;
use App\JobType;
use App\Lead;
use App\LeadSource;
use App\Postcode;
use App\Product;
use App\SalesContact;
use App\SalesStaff;
use App\Services\Interfaces\LeadServiceInterface;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LeadActualSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */


    protected $infoLogger;
    protected $postCodeLogger;
    protected $franchiseLogger;
    protected $salesStaffLogger;

    public function __construct()
    {

        // Create the logger
        $this->infoLogger = new Logger('info_logger');
        // Now add some handlers
        $this->infoLogger->pushHandler(new StreamHandler(storage_path() .'/logs/info.log', Logger::DEBUG));
        $this->infoLogger->pushHandler(new FirePHPHandler());

        // Create the logger
        $this->postCodeLogger = new Logger('postcode_logger');
        // Now add some handlers
        $this->postCodeLogger->pushHandler(new StreamHandler(storage_path() .'/logs/postcode.log', Logger::DEBUG));
        $this->postCodeLogger->pushHandler(new FirePHPHandler());

        // Create the logger
        $this->franchiseLogger = new Logger('franchise_logger');
        // Now add some handlers
        $this->franchiseLogger->pushHandler(new StreamHandler(storage_path() .'/logs/franchise.log', Logger::DEBUG));
        $this->franchiseLogger->pushHandler(new FirePHPHandler());

        // Create the logger
        $this->salesStaffLogger = new Logger('salesStaff_logger');
        // Now add some handlers
        $this->salesStaffLogger->pushHandler(new StreamHandler(storage_path() .'/logs/salesStaff.log', Logger::DEBUG));
        $this->salesStaffLogger->pushHandler(new FirePHPHandler());


    }


    public function run(LeadServiceInterface $leadService)
    {

        $leadFile = storage_path() . '/app/database/leads.csv';

        $file = fopen($leadFile, 'r');
        $count = 1;
        while (($data = fgetcsv($file)) !== FALSE) {

            $contactFirstName = trim($data[3]);
            $contactLastName = trim($data[4]);
            $contactEmail = trim($data[14]) != "" ? trim($data[14]) : 'noemail@email.com';

            $contactNumber = "";

            if(trim($data[11]) != "" ) $contactNumber .= trim($data[11]) . " ";
            if(trim($data[12]) != "" ) $contactNumber .= trim($data[11]) . " ";
            if(trim($data[13]) != "" ) $contactNumber .= trim($data[11]) . " ";

            /**
             * Need to Sanitize the Postcode Information
             */
            $pcode = trim($data[10]);
            $locality = trim($data[8]);
            $state = trim($data[9]);

          $postcode = $this->getPostcode($pcode, $locality, $state, $count);

            if($postcode == null){
                print "Postcode is Missing Postcode: {$pcode} Locality: {$locality} State: {$state} ";
                Log::error("Postcode is Missing Postcode: {$pcode} Locality: {$locality} State: {$state}");
            }

            $salesContact = SalesContact::where('first_name', $contactFirstName)
                                ->where('last_name', $contactLastName)->first();


            if($salesContact == null && $postcode != null){
                $salesContactData = [
                    'first_name' => $contactFirstName,
                    'last_name' => $contactLastName,
                    'title' => trim($data[5]),
                    'street1' => trim($data[6]),
                    'street2' => trim($data[7]),
                    'postcode_id' => $postcode->id,
                    'customer_type' => strtolower(trim($data[18])),
                    'contact_number' => $contactNumber,
                    'email' => $contactEmail
                ];
                try {
                    $salesContact = SalesContact::create($salesContactData);
                    print "SalesContact Created \n";
                    $this->infoLogger->info("Created Sales Contact {$salesContact->first_name} {$salesContact->last_name} at Count: {$count} ");
                }catch (Exception $exception){
                    print "Failed Creating Sales Contact";
                    Log::error("Error Creating Sales Contact {$contactFirstName} {$contactLastName} at Count: {$count} ");
                }


            }else {

                print "Sales Contact Already Exist \n";
            }

            $franchise = Franchise::where('franchise_number', trim($data[0]))
                            ->where('parent_id', '<>', null)->first();

            $leadSourceName = trim($data[16]);

            $leadSource = LeadSource::where('name', 'LIKE',  '%' . $leadSourceName . '%' )->first();

            if($leadSource == null){
                $leadSource = LeadSource::create(['name' => $leadSourceName]);
            }

            if($franchise != null && $leadSource != null && $salesContact != null){
                
                $leadNumber = $leadService->generateLeadNumber();

                // Need to check if LeadNumber exist. Normally when the program executes
                // in subseconds, it most like create a duplicate
                // Create a new LeadNUmber
                // while(Lead::where('lead_number', $leadNumber)->first()){
                //     $leadNumber = $leadService->generateLeadNumber();
                // }

                $leadData = [
                    'reference_number' => trim($data[1]),
                    'lead_number' => $leadNumber,
                    'franchise_id' => $franchise->id,
                    'sales_contact_id' => $salesContact->id,
                    'lead_source_id' => $leadSource->id,
                    'lead_date' => trim($data[2]),
                    'postcode_status' => Lead::INSIDE_OF_FRANCHISE
                ];

                $lead = null;
                try {
                    $lead = Lead::create($leadData);
                    print "####### Lead Created ######## \n";
                }catch (Exception $exception){
                    Log::error("Unable to create Lead {$data[1]} possible duplicate lead number");
                }

//                $lead = Lead::create($leadData);
//                print "####### Lead Created ######## \n";


                /**
                 * JobType Seeder
                 */
                $legacyName = trim($data[19]);
//                $nameArray = explode(" ", trim($data[19]));
//                $designAdvisorFirstName = $nameArray[0];
//                $designAdvisorLastName = " ";

                $matchArray = [];
                preg_match('/([a-zA-Z]+)[\s]*([a-zA-Z]*)/', $legacyName , $matchArray);

                $designAdvisorFirstName = sizeof($matchArray) > 2? $matchArray[1] : "";
                $designAdvisorLastName = sizeof($matchArray) >= 3? $matchArray[2] : "";



//                if(count($nameArray) >= 3){
//                    $designAdvisorFirstName = $nameArray[0] . " " . $nameArray[1];
//                    $designAdvisorLastName = $nameArray[2];
//                }
//
//                if(count($nameArray) == 2){
//                    $designAdvisorLastName = $nameArray[1];
//                }

                $designAdvisor = SalesStaff::where('first_name', $designAdvisorFirstName)
                                    ->where('last_name', $designAdvisorLastName)->first();

                if (!$designAdvisor){
                    $designAdvisor = SalesStaff::where('legacy_name', $legacyName)->first();
                }

                if($designAdvisor){

                    print "Sales Staff Found \n";
                    $product = Product::where('name', 'LIKE', '%' . trim($data[20]) . '%')->first();

                    if($product == null){
                        $product = Product::create(['name' => trim($data[20])]);
                    }

                    if($lead != null){
                        $jobTypeData = [
                            'taken_by' => trim($data[15]),
                            'date_allocated' => trim($data[17]),
                            'sales_staff_id' => $designAdvisor->id,
                            'lead_id' => $lead->id,
                            'description' => trim($data[21]),
                            'product_id' => $product->id
                        ];

                        try {
                            JobType::create($jobTypeData);
                            print "Lead Job Type Create \n";
                        }catch (Exception $e){
                            Log::error("Problem creating JobType. Possible data issue");
                        }


                    }


                }else {
                    $this->salesStaffLogger->alert("{$designAdvisorFirstName} {$designAdvisorLastName}");
//                    Log::alert("Sales Staff Not found {$designAdvisorFirstName} {$designAdvisorLastName} at Count: {$count}");
                    print "Sales Staff Not found {$designAdvisorFirstName} {$designAdvisorLastName} \n";
                }

                /**
                 * Appointment Seeder
                 */

                $appointmentDate = trim($data[22]);
                $reAppointmentDate = trim($data[24]);

                $actualAppointmentDate = date("Y-m-d");

                if($reAppointmentDate != "") {
                    $actualAppointmentDate = $reAppointmentDate;
                }elseif ($appointmentDate !== ""){
                    $actualAppointmentDate = $appointmentDate;
                }

                $outcome = "pending";

                if (isset($data[28])){
                    $outcome = trim($data[28]) != "" ? strtolower(trim($data[28])) : "pending";
                }

                if($lead != null){
                    $appointmentData = [
                        'appointment_date' => $actualAppointmentDate,
                        'lead_id' => $lead->id,
                        'outcome' => $outcome,
                        'comments' => trim($data[26]),
                        'quoted_price' => floatval(trim(array_key_exists(27, $data)? $data[27]: 0 ))
                    ];

                    try {
                        Appointment::create($appointmentData);
                        print "Lead Appointment Created \n";
                    }catch (Exception $e){
                        Log::error("Problem creating appointment. Possible data issue");
                    }

                }

            }else {

                print "\n#### Franchise Does Not Exist {$data[0]} ########### \n";
                $this->franchiseLogger->alert("{$data[0]}");
            }

            print "############# Item number {$count} ############## \n";
            $count++;

        }

        fclose($file);
    }



    private function getPostcode($pcode, $locality, $state, $lineCount){

        // Split Locality into 2 for those having 2 words locality
        // check for either existence of the two
        // Sometimes one of each word will have difference in character from the db record
        // Best to use Locality for Query

        $localitySplit = explode(" ", $locality);

        $postcode = null;

        if($pcode != ""){

            $postcode = Postcode::where('pcode',$pcode )
                ->where('locality', 'LIKE', '%' . $locality . '%')->first();

        }else {
            $postcode = Postcode::where('state', 'LIKE', '%' . $state . '%')
                ->where('locality', 'LIKE', '%' . $locality . '%')
                ->first();
        }

        if($postcode == null){

            // This time exclude the Postcode and Just Search for the Locality
            // Possibility is Wrong spelling on 2 words Suburb
            if(count($localitySplit) > 1){
                $postcode = Postcode::where('state', 'LIKE', '%' . $state . '%')
                    ->where(function ($query) use ($localitySplit){
                        $query->where('locality', 'LIKE', '%' . trim($localitySplit[0]) . '%')
                            ->orWhere('locality', 'LIKE', '%' . trim($localitySplit[1]) . '%');
                    })->first();
            }

        }
//
        if($postcode == null){
            // If the above query still did not return and record just get the first postcode of the State
            $postcode = Postcode::where('state', 'LIKE', '%' . $state . '%')
                ->first();
            $this->postCodeLogger->alert("Defaulting to State Postcode at {$lineCount}");
        }
//
//
        if($postcode == null){
            // If still no record default to first postcode in the database
            $this->postCodeLogger->alert("Defaulting to First Postcode at {$lineCount}");
            $postcode = Postcode::first();
        }


        return $postcode;

    }

}
