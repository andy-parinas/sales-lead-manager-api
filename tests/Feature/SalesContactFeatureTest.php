<?php

namespace Tests\Feature;

use App\Postcode;
use App\SalesContact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class SalesContactFeatureTest extends TestCase
{

    use RefreshDatabase, TestHelper;


    public function testCanCreateSalesContactByAuthenticatedUser()
    {

        $this->withoutExceptionHandling();
        
        $postcode = factory(Postcode::class)->create();
        $data = factory(SalesContact::class)->raw(['postcode' => $postcode->pcode]);

        $this->authenticateStaffUser();

        $this->post('api/contacts', $data)
            ->assertStatus(Response::HTTP_CREATED);

        $this->assertCount(1, SalesContact::all());

    }

    public function testCanNotCreateSalesContactByNonAuthenticatedUser()
    {
        $postcode = factory(Postcode::class)->create();
        $data = factory(SalesContact::class)->raw(['postcode' => $postcode->pcode]);

        $this->post('api/contacts', $data)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->assertCount(0, SalesContact::all());
    }

    public function testCanNotCreateSalesContactWithInvalidPostcode()
    {
        
        $data = factory(SalesContact::class)->raw();

        $this->authenticateStaffUser();

        $this->post('api/contacts', $data)
            ->assertStatus(Response::HTTP_BAD_REQUEST);

        $this->assertCount(0, SalesContact::all());

    }

    public function testCanListPaginatedSalesContactByAuthenticatedUSers()
    {
        factory(SalesContact::class, 30)->create();

        $this->authenticateStaffUser();


        $this->get('api/contacts')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(15, 'data');
    }

    public function testCanSortSalesContactByFields()
    {

        $this->authenticateStaffUser();


        collect(['first_name', 'last_name', 'postcode', 'suburb', 'state'])->each(function($field){
            
            $c1 = factory(SalesContact::class)->create([$field => 'AAAAAAAAAA']);
            $c2 = factory(SalesContact::class)->create([$field => 'ZZZZZZZZZZ']);

            $response =  $this->get('api/contacts?sort=' . $field . '&direction=asc');
            $result = json_decode($response->content())->data;
    
            $this->assertEquals('AAAAAAAAAA', $result[0]->{$field});
    
            $response =  $this->get('api/contacts?sort=' . $field . '&direction=desc');
            $result = json_decode($response->content())->data;
    
            $this->assertEquals('ZZZZZZZZZZ', $result[0]->{$field});

            //Delete all the record to start fresh
            $c1->delete();
            $c2->delete();


        });

    }

    public function testCanSearchSalesContactByField()
    {
        $this->authenticateStaffUser();


        collect(['first_name', 'last_name', 'postcode', 'suburb', 'state'])->each(function($field){
            
            //Needle
            factory(SalesContact::class)->create([$field => 'AAAAAAAAAA']);

            //HayStacks
            factory(SalesContact::class, 20)->create();


            $response =  $this->get('api/contacts?on=' . $field . '&search=AAAAAAAAAA');
            $result = json_decode($response->content())->data;
    
            $this->assertEquals('AAAAAAAAAA', $result[0]->{$field});

        });
    }

}
