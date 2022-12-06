<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\RoofSheetDropdown;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class LeadRoofSheetDropdownController extends ApiController
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
        $roofSheetDropdown = RoofSheetDropdown::orderBy('name', 'asc')->get();
        
        $dropdownArray = [];
        
        foreach($roofSheetDropdown as $dropdown){
            array_push($dropdownArray, $dropdown);
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
                    'name' => 'required',
                ]);
                
        $roofSheetDropdown = RoofSheetDropdown::create($data);

        $roofSheetDropdownVal = [
            'name' => $roofSheetDropdown->name,
        ];
        
        return $this->showOne($roofSheetDropdownVal, Response::HTTP_CREATED);
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

        $roofSheetDropdown = RoofSheetDropdown::findOrFail($id);

        $data = $this->validate($request, [
            'name' => 'required',
        ]);

        $roofSheetDropdown->update($data);
        
        return $this->showOne($roofSheetDropdown);
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

        $roofSheetDropdown = RoofSheetDropdown::findOrFail($id);

        $roofSheetDropdown->delete();

        return $this->showOne($roofSheetDropdown);
    }
}
