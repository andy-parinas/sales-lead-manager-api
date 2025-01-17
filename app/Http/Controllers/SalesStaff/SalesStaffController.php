<?php

namespace App\Http\Controllers\SalesStaff;

use App\Franchise;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\SalesStaffCollection;
use App\Http\Resources\SalesStaffSearchCollection;
use App\Repositories\Interfaces\SalesStafRepositoryInterface;
use App\SalesStaff;
use App\User;
use Illuminate\Http\Request;
use App\Http\Resources\SalesStaff as SalesStaffResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class SalesStaffController extends ApiController
{
    private $salesStaffRepository;

    public function __construct(SalesStafRepositoryInterface $salesStaffRepository)
    {
        $this->middleware('auth:sanctum');
        $this->salesStaffRepository = $salesStaffRepository;
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

            $salesStaffs = $this->salesStaffRepository->getAll($this->getRequestParams());
            
            $collection = new SalesStaffCollection($salesStaffs['data']);
            $count = $salesStaffs['count'];

            return $this->showApiCollection(['salesStaff' => $collection, 'count' => $count]);

        }else {

            $userFranchiseIds = $user->franchises->pluck('id')->toArray();

            $salesStaffs = $this->salesStaffRepository->getAllByFranchise($userFranchiseIds, $this->getRequestParams());

            $collection = new SalesStaffCollection($salesStaffs['data']);
            $count = $salesStaffs['count'];

            return $this->showApiCollection(['salesStaff' => $collection, 'count' => $count]);

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
        Gate::authorize('head-office-only');

        $data = $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'contact_number' => 'required',
            'sales_phone' => 'sometimes',
            'status'  => 'required'
        ]);

        $staff = SalesStaff::create($data);

        // foreach ($data['franchises'] as $franchise){

        //     $staff->franchises()->attach($franchise);

        // }

        return $this->showOne(new SalesStaffResource($staff));
    }


    public function activeSearch(Request $request)
    {
        $user = Auth::user();
        
        if($user->user_type == User::HEAD_OFFICE){
            $salesStaffs = $this->salesStaffRepository->searchAllActive($request->search);
            $allSalesStaff = $this->showAll(new SalesStaffSearchCollection($salesStaffs));
        }else {
            $userFranchiseIds = $user->franchises->pluck('id')->toArray();
            $salesStaffs = $this->salesStaffRepository->searchAllActiveByFranchise($userFranchiseIds, $request->search);
            $allSalesStaff = $this->showAll(new SalesStaffSearchCollection($salesStaffs));
        }

        return $allSalesStaff;
    }

    public function search(Request $request)
    {
        $user = Auth::user();

        if($user->user_type == User::HEAD_OFFICE){
            $salesStaffs = $this->salesStaffRepository->searchAll($request->search);
            $allSalesStaff = $this->showAll(new SalesStaffSearchCollection($salesStaffs));
        }else {
            $userFranchiseIds = $user->franchises->pluck('id')->toArray();
            $salesStaffs = $this->salesStaffRepository->searchAllByFranchise($userFranchiseIds, $request->search);
            $allSalesStaff = $this->showAll(new SalesStaffSearchCollection($salesStaffs));
        }

        return $allSalesStaff;
    }

    public function allSalesStaff()
    {
        $user = Auth::user();

        if($user->user_type == User::HEAD_OFFICE){
            $salesStaffs = $this->salesStaffRepository->getAllSalesStaff();
            $allSalesStaff = $this->showAll(new SalesStaffSearchCollection($salesStaffs));
        }else {
            $userFranchiseIds = $user->franchises->pluck('id')->toArray();
            $salesStaffs = $this->salesStaffRepository->getAllSalesStaffByFranchise($userFranchiseIds);
            $allSalesStaff = $this->showAll(new SalesStaffSearchCollection($salesStaffs));
        }

        return $allSalesStaff;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {

        $salesStaff = SalesStaff::with('franchises') ->findOrFail($id);

        return $this->showOne(new SalesStaffResource($salesStaff));
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

        $salesStaff = SalesStaff::findOrFail($id);

        $salesStaff->update($request->all());

        $salesStaff->refresh();


        return $this->showOne(new SalesStaffResource($salesStaff));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        Gate::authorize('head-office-only');

        $staff = SalesStaff::findOrFail($id);

        $staff->delete();

        return $this->showOne($staff);
    }

    public function attachFranchise($salesStaffId, $franchiseId)
    {
        Gate::authorize('head-office-only');

        $salesStaff = SalesStaff::findOrFail($salesStaffId);
        // $franchise = Franchise::findOrFail($franchiseId);
        $franchise = Franchise::where('id', $franchiseId)
            ->where('parent_id', '<>',  null)
            ->firstOrFail();

        $salesStaff->franchises()->attach($franchise->id);


        return $this->showOne($franchise);
    }

    public function detachFranchise($salesStaffId, $franchiseNumber)
    {
        Gate::authorize('head-office-only');

        $salesStaff = SalesStaff::findOrFail($salesStaffId);
        $franchise = Franchise::where('franchise_number', $franchiseNumber)
            ->where('parent_id', '<>', null)->firstOrFail();

        $salesStaff->franchises()->detach($franchise->id);


        return $this->showOne($franchise);
    }
}
