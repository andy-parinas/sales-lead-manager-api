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
    protected $contactsLogger;

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


        // Create the logger
        $this->contactsLogger = new Logger('contacts_logger');
        // Now add some handlers
        $this->contactsLogger->pushHandler(new StreamHandler(storage_path() .'/logs/contacts.log', Logger::DEBUG));
        $this->contactsLogger->pushHandler(new FirePHPHandler());


    }


    public function run(LeadServiceInterface $leadService)
    {

        $leadFile = storage_path() . '/app/database/leads.csv';

        $file = fopen($leadFile, 'r');
        $count = 1;
        while (($data = fgetcsv($file)) !== FALSE) {

            //Reset Varaibles:

            $salesContact = null;
            $franchise = null;
            $lead = null;
            $designAdvisor = null;


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

            // if($postcode == null){
            //     print "Postcode is Missing Postcode: {$pcode} Locality: {$locality} State: {$state} ";
            //     Log::error("Postcode is Missing Postcode: {$pcode} Locality: {$locality} State: {$state}");
            // }

            // $salesContact = SalesContact::where('first_name', $contactFirstName)
            //                     ->where('last_name', $contactLastName)->first();


            if($postcode != null){

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
                    $this->contactsLogger->error("Error Creating Sales Contact {$contactFirstName} {$contactLastName} at Count: {$count} ");
                }


            }else {

                print "Postcode is Missing Postcode: {$pcode} Locality: {$locality} State: {$state} ";
                $this->postCodeLogger->error("Postcode is Missing Postcode: {$pcode} Locality: {$locality} State: {$state}");
                $this->contactsLogger->error("Error Creating Sales Contact Due to missing Postcode {$contactFirstName} {$contactLastName} at Count: {$count} ");
            }

            if($salesContact != null)
            {
                dump($salesContact);

                $franchise = Franchise::where('franchise_number', trim($data[0]))
                    ->where('parent_id', '<>', null)->first();

                if($franchise != null){

                    $leadSourceName = trim($data[16]);

                    $leadSource = LeadSource::where('name', 'LIKE',  '%' . $leadSourceName . '%' )->first();

                    if($leadSource == null){
                        $leadSource = LeadSource::create(['name' => $leadSourceName]);
                    }


                    $leadNumber = $leadService->generateLeadNumber();

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


                    /**
                     * Design Advisor / Sales Staff
                     */

                    $legacyName = trim($data[19]);
                    $matchArray = [];
                    preg_match('/([a-zA-Z]+)[\s]*([a-zA-Z]*)/', $legacyName , $matchArray);
    
                    $designAdvisorFirstName = sizeof($matchArray) > 2? $matchArray[1] : "";
                    $designAdvisorLastName = sizeof($matchArray) >= 3? $matchArray[2] : "";

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




                }else { // FRANCHISE CHECK

                    print "\n#### Franchise Does Not Exist {$data[0]} ########### \n";
                    $this->franchiseLogger->alert("{$data[0]}");
                }




            }else { // SALES CONTACT CHECK
                $this->contactsLogger->error("No Contacts, NO Data was created");

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

        // $localitySplit = explode(" ", $locality);

        $postcode = null;

        if($pcode != ""){

            $postcode = Postcode::where('pcode',$pcode )
                ->where('locality', strtoupper($locality) )->first();

        }else {

            dump("here");

            $postcode = Postcode::where('state', 'LIKE', '%' . strtoupper($state) . '%')
                ->where('locality',  strtoupper($locality))
                ->first();
        }
//
        if($postcode == null && $locality != "" && $pcode != "" && $state != "" && $this->checkState($state)){
            // If the above query still did not return and record 
            // Create a new Postcode Entry
            $postcode = Postcode::create([
                'pcode' => $pcode,
                'locality' => strtoupper($locality),
                'state' => strtoupper($state)
            ]);
           
        }

        return $postcode;

    }


    private function checkState($state)
    {
        return in_array(strtoupper($state), ['ACT', 'NT', 'SA', 'WA', 'NSW', 'VIC', 'QLD', 'TAS']);
    }

}
