<?php

namespace Tests\Feature;

use App\Lead;
use App\SalesContact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\TestHelper;

class LetterFeatureTest extends TestCase
{
    use RefreshDatabase, TestHelper;


    public function testCanSendUnassingedIntroLetter()
    {

        $salesContact = factory(SalesContact::class)->create([
            'title' => 'Mr',
            'first_name' => 'Andy',
            'last_name' => 'Parinas',
            'email' => 'andyp@crystaltec.com.au'
        ]);

        $lead = factory(Lead::class)->create([
            'sales_contact_id' => $salesContact->id
        ]);

        $this->authenticateHeadOfficeUser();

        $this->post('api/leads/'. $lead->id .'/letters/unassigned-intro/' . $salesContact->id)
            ->assertStatus(Response::HTTP_OK);

    }


    public function testCanSendAssingedIntroLetter()
    {

        $salesContact = factory(SalesContact::class)->create([
            'title' => 'Mr',
            'first_name' => 'Andy',
            'last_name' => 'Parinas',
            'email' => 'andyp@crystaltec.com.au'
        ]);

        $lead = factory(Lead::class)->create([
            'sales_contact_id' => $salesContact->id
        ]);

        $this->authenticateHeadOfficeUser();

        $this->post('api/leads/'. $lead->id .'letters/assigned-intro/' . $salesContact->id)
            ->assertStatus(Response::HTTP_OK);

    }

    public function testCanSendWelcomeLetter()
    {

        $salesContact = factory(SalesContact::class)->create([
            'title' => 'Mr',
            'first_name' => 'Andy',
            'last_name' => 'Parinas',
            'email' => 'andyp@crystaltec.com.au'
        ]);

        $this->authenticateHeadOfficeUser();

        $this->post('api/letters/welcome/' . $salesContact->id)
            ->assertStatus(Response::HTTP_OK);

    }

    public function testCanSendCouncilIntroLetter()
    {

        $salesContact = factory(SalesContact::class)->create([
            'title' => 'Mr',
            'first_name' => 'Andy',
            'last_name' => 'Parinas',
            'email' => 'andyp@crystaltec.com.au'
        ]);

        $this->authenticateHeadOfficeUser();

        $this->post('api/letters/council-intro/' . $salesContact->id)
            ->assertStatus(Response::HTTP_OK);

    }


    public function testCanSendNoCouncilLetter()
    {

        $salesContact = factory(SalesContact::class)->create([
            'title' => 'Mr',
            'first_name' => 'Andy',
            'last_name' => 'Parinas',
            'email' => 'andyp@crystaltec.com.au'
        ]);

        $this->authenticateHeadOfficeUser();

        $this->post('api/letters/no-council/' . $salesContact->id)
            ->assertStatus(Response::HTTP_OK);

    }

    public function testCanSendOutOfCouncilLetter()
    {

        $salesContact = factory(SalesContact::class)->create([
            'title' => 'Mr',
            'first_name' => 'Andy',
            'last_name' => 'Parinas',
            'email' => 'andyp@crystaltec.com.au'
        ]);

        $this->authenticateHeadOfficeUser();

        $this->post('api/letters/out-of-council/' . $salesContact->id)
            ->assertStatus(Response::HTTP_OK);

    }
}
