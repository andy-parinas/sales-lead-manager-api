<?php

namespace App\Http\Controllers\Lead;

// use App\WarrantyClaimDropdown;
// use App\Http\Controllers\ApiController;
// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;
// use App\Http\Resources\WarrantyClaimDropdownCollection as WarrantyClaimDropdownResource;
// use Symfony\Component\HttpFoundation\Response;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\WarrantyClaimDropdown;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class LeadWarrantyClaimDropdownController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $warrantyClaimDropdown = WarrantyClaimDropdown::orderBy('type', 'asc')->get();
        
        $dropdownArray = [];

        foreach($warrantyClaimDropdown as $dropdown){
            $typeText = '';
            if($dropdown->type == 'complaint_received_via'){
                $typeText = 'Complaint Received Via';
            } else if($dropdown->type == 'home_addition'){
                $typeText = 'Home Addition';
            } else if($dropdown->type == 'complaint_status'){
                $typeText = 'Complaint Status';
            } else {
                $typeText = 'Complaint';
            }

            $dropdownVal = [
                'id' => $dropdown->id,
                'type' => $typeText,
                'description' => $dropdown->description,
                'createad_at' => $dropdown->created_at,
                'updated_at' => $dropdown->updated_at,
            ];

            array_push($dropdownArray, $dropdownVal);
        }

        return $this->showAll($dropdownArray);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('head-office-only');

        $data = $this->validate($request, [
                    'type' => 'required',
                    'description' => 'required',
                ]);
                
        $warrantyClaimDropdown = WarrantyClaimDropdown::create($data);

        $typeText = '';
        if($warrantyClaimDropdown->type == 'complaint_received_via'){
            $typeText = 'Complaint Received Via';
        } else if($warrantyClaimDropdown->type == 'home_addition'){
            $typeText = 'Home Addition';
        } else if($warrantyClaimDropdown->type == 'complaint_status'){
            $typeText = 'Complaint Status';
        } else {
            $typeText = 'Complaint';
        }

        $warrantyClaimDropdownVal = [
            'type' => $typeText,
            'description' => $warrantyClaimDropdown->description,
        ];
        
        return $this->showOne($warrantyClaimDropdownVal, Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        Gate::authorize('head-office-only');

        $warrantyClaimDropdown = WarrantyClaimDropdown::findOrFail($id);

        $data = $this->validate($request, [
            'type' => 'required',
            'description' => 'required',
        ]);

        $newTypeText = '';
        $typeText = '';
        if($request->type == 'Complaint Received Via' || $request->type == 'complaint_received_via'){
            $newTypeText = 'complaint_received_via';
            $typeText = 'Complaint Received Via';
        } else if($request->type == 'Home Addition' || $request->type == 'home_addition'){
            $newTypeText = 'home_addition';
            $typeText = 'Home Addition';
        } else if($request->type == 'Complaint Status' || $request->type == 'complaint_status'){
            $newTypeText = 'complaint_status';
            $typeText = 'Complaint Status';
        } else {
            $newTypeText = 'complaint';
            $typeText = 'Complaint';
        }
        $data['type'] = $newTypeText;

        $warrantyClaimDropdown->update($data);

        $warrantyClaimDropdown['type'] = $typeText;
        return $this->showOne($warrantyClaimDropdown);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dropdownText($type)
    {
        $warrantyDropdown = WarrantyClaimDropdown::where('type', $type)->get();
        return $this->showAll($warrantyDropdown);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Gate::authorize('head-office-only');

        $warrantyClaimDropdown = WarrantyClaimDropdown::findOrFail($id);

        $warrantyClaimDropdown->delete();

        return $this->showOne($warrantyClaimDropdown);
    }
}
