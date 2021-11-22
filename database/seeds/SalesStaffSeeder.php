<?php

use App\Franchise;
use App\SalesStaff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class SalesStaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $leadFile = storage_path() . '/app/database/sales_staff.csv';

        $file = fopen($leadFile, 'r');
        $count = 1;
        while (($data = fgetcsv($file)) !== FALSE) {
            $salesFranchise = trim($data[0]);
            $status = strtolower(trim($data[1]));
            $salesPhone = trim($data[3]);
            $salesEmail = trim($data[4]);
            $legacyName = trim($data[2]);

            $matchArray = [];
            preg_match('/([a-zA-Z]+)[\s]*([a-zA-Z]*)/', $legacyName , $matchArray);

            $firstName = sizeof($matchArray) > 2? $matchArray[1] : "";
            $lastName = sizeof($matchArray) >= 3? $matchArray[2] : "";

            dump("{$firstName} {$lastName}");

            $salesStaff = SalesStaff::where('first_name', $firstName)
                   ->where('last_name', $lastName)->first();


           $franchise = Franchise::where('franchise_number', $salesFranchise)
                ->where('parent_id', '<>', null)->first();

           if ($franchise !== null){

               if($salesStaff == null)
               {

                   $data = [
                       'first_name' => $firstName,
                       'last_name' => $lastName,
                       'legacy_name' => $legacyName,
                       'contact_number' => $salesPhone,
                       'sales_phone' => $salesPhone,
                       'email' => $salesEmail,
                       'status' => $status == 'active'? SalesStaff::ACTIVE : SalesStaff::BLOCKED
                   ];

                   $salesStaff = SalesStaff::create($data);
                   $salesStaff->franchises()->attach($franchise->id);
                   print "Sales staff Created {$firstName} {$lastName} \n";

               }else {
                   $salesStaffFranchisesArray = $salesStaff->franchises->pluck('id')->toArray();

                    if(!in_array($franchise->id, $salesStaffFranchisesArray)){
                        $salesStaff->franchises()->attach($franchise->id);
                        print "Sales Staff Exist {$firstName} {$lastName} - Franchise Assigned {$salesFranchise} \n";
                    }
               }

           }else {
               print "No Sales Staff Created - Franchise Not Found {$salesFranchise} \n";
           }


        }

    }
}
