<?php

namespace Tests\Feature;

use App\Contract;
use App\Lead;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class LeadContractVariationFeatureTest extends TestCase
{
    use RefreshDatabase, TestHelper;


    public function testCanCreateContractVariationsPositiveAmount()
    {

            $lead = factory(Lead::class)->create();

            $contract = factory(Contract::class)->create([
                'contract_price' => 100.00,
                'deposit_amount' => 25.00,
                'total_contract' => 75.00,
                'lead_id' => $lead->id
            ]);


            $data = [
                'variation_date' => '06/29/2020',
                'description' => 'Test Contract Variation',
                'amount' => 15.00,
            ];

            $this->authenticateStaffUser();

            $response = $this->post('api/contracts/' . $contract->id . '/contract-variations', $data);

            $response->assertStatus(Response::HTTP_CREATED);

            $contract->refresh();

            $this->assertEquals(15, $contract->total_variation);
            $this->assertEquals(90, $contract->total_contract);
            $this->assertCount(1, $contract->contractVariations);
            //dd(json_decode($response->content()));

    }


    public function testCanCreateContractVariationsNegativeAmount()
    {

        $lead = factory(Lead::class)->create();

        $contract = factory(Contract::class)->create([
            'contract_price' => 100.00,
            'deposit_amount' => 25.00,
            'total_contract' => 75.00,
            'lead_id' => $lead->id
        ]);


        $data = [
            'variation_date' => '06/29/2020',
            'description' => 'Test Contract Variation',
            'amount' => -15.00,
        ];

        $this->authenticateStaffUser();
        $response = $this->post('api/contracts/' . $contract->id . '/contract-variations', $data);

        $response->assertStatus(Response::HTTP_CREATED);

        $contract->refresh();

        $this->assertEquals(-15, $contract->total_variation);
        $this->assertEquals(60, $contract->total_contract);
        $this->assertCount(1, $contract->contractVariations);
        //dd(json_decode($response->content()));

    }


    public function testCanNotCreateVariationsResultingNegativeTotalContract()
    {

        $lead = factory(Lead::class)->create();

        $contract = factory(Contract::class)->create([
            'contract_price' => 100.00,
            'deposit_amount' => 25.00,
            'total_contract' => 75.00,
            'lead_id' => $lead->id
        ]);


        $data = [
            'variation_date' => '06/29/2020',
            'description' => 'Test Contract Variation',
            'amount' => -150.00,
        ];

        $this->authenticateStaffUser();
        $response = $this->post('api/contracts/' . $contract->id . '/contract-variations', $data);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);

        $contract->refresh();

        $this->assertEquals(0, $contract->total_variation);
        $this->assertEquals(75, $contract->total_contract);
        $this->assertCount(0, $contract->contractVariations);

        //dd(json_decode($response->content()));

    }


}
