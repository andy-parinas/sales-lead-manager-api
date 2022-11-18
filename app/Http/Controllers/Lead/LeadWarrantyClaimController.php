<?php

namespace App\Http\Controllers\Lead;

use App\WarrantyClaim;
use App\CustomerReview;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Lead;
use Illuminate\Http\Request;
use App\Http\Resources\WarrantyClaimCollection as WarrantyClaimResource;
use Symfony\Component\HttpFoundation\Response;

class LeadWarrantyClaimController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $leadId)
    {
        $lead = Lead::findOrFail($leadId);

        $warrantyClaim = $lead->WarrantyClaim;

        if($warrantyClaim == null){
            return response()->json([], Response::HTTP_NO_CONTENT);
        }

        return $this->showOne(new WarrantyClaimResource($warrantyClaim));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $leadId)
    {
        $lead = Lead::findOrFail($leadId);
        
        $data = $this->validate($request, [
            'date_complaint' => 'required',
            'complaint_received' => 'required',
            'complaint_type' => 'required',
            'home_addition_type' => 'required',
            'complaint_description' => 'sometimes',
            'status' => 'required',
        ]);

        $data['contacted_franchise'] = (isset($request->contacted_franchise))? $request->contacted_franchise : 'No';
        $data['date_complaint_closed'] = ($request->date_complaint_closed == '') ? '0000-00-00' : $request->date_complaint_closed;

        $warrantyClaim = $lead->WarrantyClaim()->create($data);
        
        return $this->showOne(new WarrantyClaimResource($warrantyClaim), Response::HTTP_CREATED);
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $leadId, $warrantyClaimId)
    {
        $lead = Lead::findOrFail($leadId);

        $warrantyClaim = WarrantyClaim::findOrFail($warrantyClaimId);

        if($warrantyClaim->lead_id != $lead->id){
            abort(Response::HTTP_BAD_REQUEST, "Lead and Verification do not match");
        }

        $data = $this->validate($request, [
            'date_complaint' => 'required',
            'complaint_received' => 'required',
            'complaint_type' => 'required',
            'home_addition_type' => 'required',
            'complaint_description' => 'sometimes',
            'status' => 'required',
        ]);

        $data['contacted_franchise'] = $request->contacted_franchise;
        $data['date_complaint_closed'] = $request->date_complaint_closed;
        
        $warrantyClaim->update($data);

        return $this->showOne(new WarrantyClaimResource($warrantyClaim));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
