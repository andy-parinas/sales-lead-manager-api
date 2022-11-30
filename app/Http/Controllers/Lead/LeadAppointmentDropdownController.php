<?php

namespace App\Http\Controllers\Lead;

use App\Appointment;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\AppointmentDropdown;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LeadAppointmentDropdownController extends ApiController
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
        $appointmentDropdown = AppointmentDropdown::orderBy('type', 'asc')->get();
        
        $dropdownArray = [];

        foreach($appointmentDropdown as $dropdown){

            $typeText = '';
            if($dropdown->type == 'customer_touch_point'){
                $typeText = 'Customer Touch Point';
            } else {
                $typeText = 'Outcome';
            }

            $dropdownVal = [
                'id' => $dropdown->id,
                'name' => $dropdown->name,
                'type' => $typeText,
                'createad_at' => $dropdown->created_at,
                'updated_at' => $dropdown->updated_at,
            ];

            array_push($dropdownArray, $dropdownVal);
        }

        return $this->showAll($dropdownArray);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dropdownText($type)
    {
        $warrantyDropdown = AppointmentDropdown::where('type', $type)->get();
        return $this->showAll($warrantyDropdown);
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
                    'name' => 'required',
                ]);
                
        $appointmentDropdown = AppointmentDropdown::create($data);

        $typeText = '';
        if($request->type == 'customer_touch_point'){
            $typeText = 'Customer Touch Point';
        } else {
            $typeText = 'Outcome';
        }

        $appointmentDropdownVal = [
            'type' => $typeText,
            'name' => $appointmentDropdown->name,
        ];
        
        return $this->showOne($appointmentDropdownVal, Response::HTTP_CREATED);
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
        
        $appointmentDropdown = AppointmentDropdown::findOrFail($id);
        
        $data = $this->validate($request, [
            'type' => 'required',
            'name' => 'required',
        ]);
        
        $newTypeText = '';
        $typeText = '';
        if($request->type == 'Customer Touch Point'){
            $newTypeText = 'customer_touch_point';
            $typeText = 'Customer Touch Point';
        } else {
            $newTypeText = 'outcome';
            $typeText = 'Outcome';
        }
        //dd($request->type);
        $data['type'] = $newTypeText;

        $appointmentDropdown->update($data);
        $appointmentDropdown['type'] = $typeText;
        return $this->showOne($appointmentDropdown);
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

        $appointmentDropdown = AppointmentDropdown::findOrFail($id);

        $appointmentDropdown->delete();

        return $this->showOne($appointmentDropdown);
    }
}
