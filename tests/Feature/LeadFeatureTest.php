<?php

namespace Tests\Feature;

use App\Franchise;
use App\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class LeadFeatureTest extends TestCase
{
    use RefreshDatabase, TestHelper;


    public function testCanListAllLeadsByHeadOfficeUsers()
    {

        // Each Lead here will have its own Franchise. 
        // this should be listed by HeadOffice User without referencing to a franchise
        factory(Lead::class, 10)->create();

        Sanctum::actingAs(
            $this->createHeadOfficeUser(),
            ['*']
        );

        $this->get('api/leads/')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(10, 'data');
    }

    public function testCanNotListAllLeadsNyNonHeadOfficeUsers()
    {
        
        factory(Lead::class, 10)->create();

        Sanctum::actingAs(
            $this->createFranchiseAdminUser(),
            ['*']
        );

        $this->get('api/leads/')
            ->assertStatus(Response::HTTP_FORBIDDEN);

        
        Sanctum::actingAs(
            $this->createStaffUser(),
            ['*']
        );

        $this->get('api/leads/')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testCanShowLeadWithoutReferenceToFranchiseByHeadOfficeUsers( )
    {

        $this->withoutExceptionHandling();

        $lead = factory(Lead::class)->create();

        Sanctum::actingAs(
            $this->createHeadOfficeUser(),
            ['*']
        );

        $response = $this->get('api/leads/'. $lead->id);
        $result = json_decode($response->content())->data;

        $response->assertStatus(Response::HTTP_OK);

        $this->assertEquals($lead->number, $result->number);
    }

    public function testCanNotShowLeadWithOutFranchiseReferenceByNonHeadOffice()
    {
        // $this->withoutExceptionHandling();

        $lead = factory(Lead::class)->create();

        Sanctum::actingAs(
            $this->createStaffUser(),
            ['*']
        );

        $this->get('api/leads/'. $lead->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        Sanctum::actingAs(
            $this->createFranchiseAdminUser(),
            ['*']
        );

        $this->get('api/leads/'. $lead->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

}