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
    protected $leadLogger;

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

        $this->leadLogger = new Logger('leads_logger');
        // Now add some handlers
        $this->leadLogger->pushHandler(new StreamHandler(storage_path() .'/logs/leads.log', Logger::DEBUG));
        $this->leadLogger->pushHandler(new FirePHPHandler());


    }


    public function run(LeadServiceInterface $leadService)
    {

        $leadFile = storage_path() . '/app/database/leads.csv';

        $file = fopen($leadFile, 'r');
        $count = 1;
        while (($data = fgetcsv($file)) !== FALSE) {

            //Reset Varaibles:
            // sleep(2);
            

            $salesContact = null;
            $franchise = null;
            $lead = null;
            $designAdvisor = null;


            $contactFirstName = trim($data[3]);
            $contactLastName = trim($data[4]);
            $contactEmail = trim($data[14]) != "" ? trim($data[14]) : 'noemail@email.com';
            $referenceNumber =  trim($data[1]);

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

            $postcode = $this->getPostcode($pcode, $locality, $state, $referenceNumber, $contactLastName );


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
                $this->postCodeLogger->error("Postcode is null no lead is created. Reference: {$referenceNumber}");
            }

            if($salesContact != null)
            {
                

                $franchise = Franchise::where('franchise_number', trim($data[0]))
                    ->where('parent_id', '<>', null)->first();

                if($franchise != null){

                    $leadSourceName = trim($data[16]);

                    $leadSource = LeadSource::where('name', 'LIKE',  '%' . $leadSourceName . '%' )->first();

                    if($leadSource == null){
                        $leadSource = LeadSource::create(['name' => $leadSourceName]);
                    }


                    $leadNumber = $leadService->generateLeadNumber();

                    $leadDate = trim($data[2]);

                    $leadData = [
                        'reference_number' => trim($data[1]),
                        'lead_number' => $leadNumber,
                        'franchise_id' => $franchise->id,
                        'sales_contact_id' => $salesContact->id,
                        'lead_source_id' => $leadSource->id,
                        'lead_date' => $leadDate,
                        'postcode_status' => Lead::INSIDE_OF_FRANCHISE
                    ];

                    $lead = null;

                    try {

                        $lead = Lead::create($leadData);
                        print "####### Lead Created ######## \n";

                    }catch (Exception $exception){

                        if($exception->getCode() == 22007){
                            $this->leadLogger->error("[Reference: {$referenceNumber}| Lastname: {$contactLastName}] Unable to create Lead. possible Lead Date issue {$leadDate}");
                        }else {
                            $errorData = json_encode($leadData);
                            $this->leadLogger->error("[Reference: {$referenceNumber}| Lastname: {$contactLastName}] Unable to create Lead. Data issue {$errorData}");
                            Log::error($exception->getMessage());
                        }
                        
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


                            $dateAllocated =  trim($data[17]);
                            $jobTypeData = [
                                'taken_by' => trim($data[15]),
                                'date_allocated' => $dateAllocated,
                                'sales_staff_id' => $designAdvisor->id,
                                'lead_id' => $lead->id,
                                'description' => trim($data[21]),
                                'product_id' => $product->id
                            ];
    
                            try {
                                JobType::create($jobTypeData);
                                print "Lead Job Type Create \n";

                            }catch (Exception $exception){

                                if($exception->getCode() == 22007){
                                    $this->leadLogger->error("[Reference: {$referenceNumber}| Lastname: {$contactLastName}] Unable to create Job Type. possible Date Allocated issue {$dateAllocated}");
                                }else {
                                    $errorData = json_encode($jobTypeData);
                                    $this->leadLogger->error("[Reference: {$referenceNumber}| Lastname: {$contactLastName}] Unable to create Job Type. Data issue {$errorData}");
                                    Log::error($exception->getMessage());
                                }
                               
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
                        }catch (Exception $exception){
                            if($exception->getCode() == 22007){
                                $this->leadLogger->error("[Reference: {$referenceNumber}| Lastname: {$contactLastName}] Unable to create Appointments. possible Appointment Date issue {$actualAppointmentDate}");
                            }else {
                                $errorData = json_encode($appointmentData);
                                $this->leadLogger->error("[Reference: {$referenceNumber}| Lastname: {$contactLastName}] Unable to create Appointment. Data issue {$errorData}");
                                Log::error($exception->getMessage());
                            }
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



    private function getPostcode($pcode, $locality, $state, $reference, $contactLastName){


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
                $this->postCodeLogger->info("[Reference: {$reference}| Lastname: {$contactLastName}]  Using Only postcode {$pcode} Data");
                return $postcode;
            }


        }elseif($locality != ""){ //Assume pcode has no value

            dump("Postcode {$pcode} Missing | Locality {$locality} Available");


            if($state != "" && $this->checkState($state)){

                dump("Locality {$locality} Available and State {$state} Available");


                $postcode = Postcode::where('locality',strtoupper($locality) )
                    ->where('state', strtoupper($state) )->first();

                if($postcode != null) {
                    dump("Locality {$locality} Available and State {$state} Valid returning");
                    $this->postCodeLogger->info("[Reference: {$reference}| Lastname: {$contactLastName}]  defaulted to postcode {$postcode->pcode} using Locality: {$locality} and State: {$state} data");
                    return $postcode;
                }

            }else {

                dump("Locality {$locality} Available and State {$state} Not Valid");


                $postcode = Postcode::where('locality', strtoupper($locality) )->first();

                if($postcode != null) {
                    dump("defaulted to postcode {$postcode->pcode} of Locality: {$locality} returning");
                    $this->postCodeLogger->info("[Reference: {$reference}| Lastname: {$contactLastName}]  defaulted to postcode {$postcode->pcode} of Locality: {$locality}");
                    return $postcode;
                }
            }

        }elseif($state != "" && $this->checkState($state)){

            dump("Only State {$state} is Valid");

            $postcode = Postcode::where('state', strtoupper($state))->first();

            if($postcode != null) {
                dump(" defaulted to first postcode {$postcode->pcode} of State: {$state} returning");
                $this->postCodeLogger->info("[Reference: {$reference}| Lastname: {$contactLastName}] defaulted to first postcode {$postcode->pcode} of State: {$state}");
                return $postcode;
            }
        }
        
        
        if($postcode != "" && (int)$postcode != 0 && $locality != "" && $state != "" && $this->checkState($state)) {

            dump("No Valid postcode data can be found. But Can Create a postocde. Will create postcode entry");
            dump("Postcode: {$pcode} | Locality {$locality} | State {$state}");


            $postcode = Postcode::create([
                'state'=> strtoupper($state),
                'pcode' => $pcode,
                'locality' => strtoupper($locality)
            ]);

            if($postcode != null) {
                $this->postCodeLogger->info("[Reference: {$reference}| Lastname: {$contactLastName}] Created a postcode entry {$postcode->pcode} State: {$state} Locality: {$locality}");
                return $postcode;
            }else {
                $this->postCodeLogger->info("[Reference: {$reference}| Lastname: {$contactLastName}] Unable to create a postcode entry {$pcode} State: {$state} Locality: {$locality}");
            }
        }else {
            $this->postCodeLogger->info("[Reference: {$reference}| Lastname: {$contactLastName}] Data not valid to create postcode {$pcode} State: {$state} Locality: {$locality}");
        }

        // IF cannot find or cannot create a postcode due to incorect or missing data
        // Get the first postcode in the database
        if($postcode == null){

            dump("No Valid postcode data can be found. Can not Create Postcode. Defaulting to First Postcode");


            $postcode = Postcode::first();
            $this->postCodeLogger->info("[Reference: {$reference}| Lastname: {$contactLastName}] Missing and Incorect data. Getting the first postcode in the database. {$postcode->pcode} State: {$postcode->state} Locality: {$postcode->locality}");

        }

        return $postcode;

    }


    private function checkState($state)
    {
        return in_array(strtoupper($state), ['ACT', 'NT', 'SA', 'WA', 'NSW', 'VIC', 'QLD', 'TAS']);
    }


    private function validateDate($date, $format = 'Y-m-d h:m:s.u')
    {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }

}
