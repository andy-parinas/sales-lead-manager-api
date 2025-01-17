<?php

use App\Franchise;
use App\Lead;
use Illuminate\Database\Seeder;

class CustomerSatisfactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $satisfactionFile = storage_path() . '/app/database/customer_satisfaction.csv';


        $file = fopen($satisfactionFile, 'r');
        $count = 1;
        while (($data = fgetcsv($file)) !== FALSE) {

            $franchiseNumber = trim($data[0]);
            $leadReferenceNumber = trim($data[1]);
            $dateProjectCompleted = trim($data[2]);
            $dateWarrantyReceive = trim($data[3]);
            $homeAdditionType = trim($data[4]);
            $homeAdditionDescription = trim($data[5]);
            $serviceRating = trim($data[6]);
            $workmanshipRating = trim($data[7]);
            $finishProductRating = trim($data[8]);
            $designConsultantRating = trim($data[9]);
            $comments = trim($data[10]);

            $franchise = Franchise::where('franchise_number', $franchiseNumber)
                ->where('parent_id', '<>', null)
                ->first();

            if($franchise != null){

                $lead = Lead::where('reference_number', $leadReferenceNumber)
                    ->where('franchise_id', $franchise->id)
                    ->first();

                if($lead != null){

                    $data = [
                        'date_project_completed' => $dateProjectCompleted,
                        'date_warranty_received' => $dateWarrantyReceive,
                        'home_addition_type' => $homeAdditionType,
                        'home_addition_description' => $homeAdditionDescription,
                        'service_received_rating' => $serviceRating,
                        'workmanship_rating' => $workmanshipRating,
                        'finished_product_rating' => $finishProductRating,
                        'design_consultant_rating' => $designConsultantRating,
                        'comments' => $comments,
                    ];

                    try{
                        
                        $lead->customerReview()->create($data);

                        print "Customer Review Created For {$lead->lead_number} Count: {$count} ";

                    }catch(Exception $exception)
                    {
                        if($exception->getCode() == 22007){
                            $this->logger->error("[Reference: {$leadReferenceNumber}| Franchise: {$franchiseNumber}] Unable to create Customer Review due to invalid date");
                        }else {
                            $this->logger->error("[Reference: {$leadReferenceNumber}| Franchise: {$franchiseNumber}] Unable to create Customer Review due to invalid data");
                        }

                    }

                   

                }else {

                    print "No Lead Found Lead: {$leadReferenceNumber}, FranchiseId: {$franchise->id}, FranchiseNumber: {$franchise->franchise_number} Count: {$count} \n";

                }

            }else {

                print "No Franchise Found Franchise Number: $franchiseNumber Count: {$count} \n";

            }

            print "\n########## Count Number {$count} ################### \n";
            $count++;

        }
    }
}
