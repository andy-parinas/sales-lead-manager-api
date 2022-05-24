<?php

namespace Tests\Feature;

use App\SalesStaff;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class ReportFeatureTest extends TestCase
{

    use TestHelper;

//    public function setUp(): void
//    {
//        parent::setUp();
//
//        Artisan::call('db:seed', ['--class' => 'FranchiseSeeder']);
//        Artisan::call('db:seed', ['--class' => 'ProductSeeder']);
//        Artisan::call('db:seed', ['--class' => 'LeadSourceSeeder']);
//        Artisan::call('db:seed', ['--class' => 'LeadTestSeeder']);
//
//    }

    public function testSalesSummaryReport()
    {

        $this->withoutExceptionHandling();

        $this->authenticateHeadOfficeUser();

        $response = $this->get('api/reports/customer-reviews?start_date=2020-01-01&end_date=2020-07-19&franchise_id=9');

        dd(json_decode($response->content()));

        $response->assertStatus(Response::HTTP_OK);


    }


    public function testLeadAndContractReport()
    {

        $this->withoutExceptionHandling();


        $user = User::where('username', 'staffuser-2650')->first();

        Sanctum::actingAs(
            $user,
            ['*']
        );

        $response = $this->get('api/reports/lead-contract?start_date=2021-01-01&end_date=2022-05-24');

        // dd("End");
        dd(json_decode($response->content()));

        $response->assertStatus(Response::HTTP_OK);


    }




}
