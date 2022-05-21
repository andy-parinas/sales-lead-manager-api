<?php

use App\Appointment;
use App\Franchise;
use App\JobType;
use App\Lead;
use App\LeadSource;
use App\Postcode;
use App\Product;
use App\SalesContact;
use App\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{




    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            // FranchiseSeeder::class,
            // PostcodeSeeder::class,
            // FranchisePostcodeSeeder::class,
            // UserActualSeeder::class,
            // SalesStaffSeeder::class,
            // TradeStaffActualSeeder::class,
            LeadActualSeeder::class,
            ContractActualSeeder::class,
            ContractVariationActualSeeder::class,
            FinanceSeeder::class,
            PaymentSeeder::class,
            // RoofSheetSeeder::class,
            // RoofColourSeeder::class,
            ConstructionActualSeeder::class,
            BuildingAuthoritySeeder::class,
            VerificationSeeder::class,
            CustomerSatisfactionSeeder::class
        ]);

    }
}
