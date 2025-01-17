<?php

namespace App\Http\Controllers\SalesContact;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\SalesContactCollection;
use App\Postcode;
use App\Repositories\Interfaces\SalesContactRepositoryInterface;
use App\SalesContact;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Resources\SalesContact as SalesContactResource;
use Illuminate\Support\Facades\Gate;

class SalesContactController extends ApiController
{

    private $salesContactRepository;

    public function __construct(SalesContactRepositoryInterface $salesContactRepository) {
        $this->middleware('auth:sanctum');
        $this->salesContactRepository = $salesContactRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {

        $user = Auth::user();

        if($user->user_type == User::HEAD_OFFICE){

            $salesContacts = $this->salesContactRepository->sortAndPaginate($this->getRequestParams());

            return $this->showApiCollection(new SalesContactCollection($salesContacts));

        }else {

            $postcodeIds = [];

            $franchises = $user->franchises;

            foreach ($franchises as $franchise){

                $postcodes = $franchise->postcodes->pluck('id')->toArray();

                $postcodeIds = array_merge($postcodeIds, $postcodes);

            }

            $salesContacts = $this->salesContactRepository->sortAndPaginateByFranchise($postcodeIds, $this->getRequestParams());

            return $this->showApiCollection(new SalesContactCollection($salesContacts));

        }




    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'title' => 'string|nullable',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'required|email',
            'email2' => 'nullable|email',
            'contact_number' => 'required|string',
            'street1' => 'required|string',
            'street2' => 'string|nullable',
            'postcode_id' => 'required',
            'customer_type' => 'required|in:' . SalesContact::COMMERCIAL . ',' . SalesContact::RESIDENTIAL,
            'status' => 'in:'. SalesContact::ACTIVE . ',' . SalesContact::ARCHIVED,
        ]);

        $postcode = Postcode::find($data['postcode_id']);

        if($postcode === null){
            return $this->errorResponse("Invalid postcode", Response::HTTP_BAD_REQUEST );
        }

        $salesContact = SalesContact::create($data);

        return $this->showOne(new SalesContactResource($salesContact), Response::HTTP_CREATED);


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $contact = SalesContact::with(['leads', 'postcode'])->findOrFail($id);

        return $this->showOne(new SalesContactResource($contact));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SalesContact $contact)
    {
        $data = $this->validate($request, [
            'title' => 'string',
            'first_name' => 'string|max:50',
            'last_name' => 'string|max:50',
            'email' => 'email',
            'email2' => 'nullable|email',
            'contact_number' => '|string',
            'street1' => 'string',
            'street2' => 'string|nullable',
            'suburb' => 'string',
            'state' => 'string',
            'postcode_id' => 'integer',
            'customer_type' => 'in:' . SalesContact::COMMERCIAL . ',' . SalesContact::RESIDENTIAL,
            'status' => 'in:'. SalesContact::ACTIVE . ',' . SalesContact::ARCHIVED,
        ]);

//        dd($data);

//        if(($request['postcode'] || $request['state'] || $request['suburb']) && $contact->leads()->count() > 0)
//        {
//            return $this->errorResponse("Cannot update postode, state, or suburb when Contact is already a lead", Response::HTTP_BAD_REQUEST);
//        }

//        if($request['postcode'] && $contact->leads()->count() > 0)
//        {
//            return $this->errorResponse("Cannot update postode, state, or suburb when Contact is already a lead", Response::HTTP_BAD_REQUEST);
//        }

        $contact->update($data);
        $contact->refresh();

        return $this->showOne(new SalesContactResource($contact));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(SalesContact $contact)
    {

        Gate::authorize('head-office-only');

        $contact->delete();

        return $this->showOne($contact);
    }


    public function search(Request $request)
    {   
        $user = Auth::user();
        $postcodeIds = null;

        if($user->user_type == User::HEAD_OFFICE){            
            $salesContacts = $this->salesContactRepository->simpleSearch($this->getRequestParams());            
        } else {
            $postcodeIds = [];
            $franchises = $user->franchises;
            
            foreach ($franchises as $franchise){
                $postcodes = $franchise->postcodes->pluck('id')->toArray();
                $postcodeIds = array_merge($postcodeIds, $postcodes);
            }

            $salesContacts = $this->salesContactRepository->simpleSearchByFranchise($this->getRequestParams(), $postcodeIds);
        }

        return $this->showApiCollection(new SalesContactCollection($salesContacts));
    }
}
