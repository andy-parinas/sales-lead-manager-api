<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Lead;
use App\Repositories\Interfaces\LeadTransferRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Lead as LeadResource;
use Symfony\Component\HttpFoundation\Response;

class LeadTransferController extends ApiController
{
    private $leadTransferRepository;

    public function __construct(
        LeadTransferRepositoryInterface $leadTransferRepository
    ) {
        $this->middleware('auth:sanctum');
        $this->leadTransferRepository = $leadTransferRepository;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isHeadOffice()){
            $leads = $this->leadTransferRepository->getAllLeads($this->getRequestParams());
            return $this->showPaginated($leads);
        }

        $franchiseIds = $user->franchises->pluck('id')->toArray();
        $leads = $this->getLeadByFranchiseId->findLeadsByUsersFranchise($franchiseIds, $this->getRequestParams());
        
        return $this->showPaginated($leads);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $leads = $this->leadTransferRepository->transferFranchises($request->new_franchise_id, $id);
        return $leads;
    }

    /**
     * Listing of leads under franchise.
     *
     * @return \Illuminate\Http\Response
     */
    public function byFranchiseId($franchiseId)
    {
        $leads = $this->leadTransferRepository->getAllLeadsByFranchiseId($franchiseId, $this->getRequestParams());
        return $this->showPaginated($leads);
    }

    /**
     * Count total franchise in leads.
     *
     * @return \Illuminate\Http\Response
     */
    public function totalFranchiseInLeads($id)
    {
        $leads = $this->leadTransferRepository->getTotalFranchiseInLeads($id);
        return $leads;
    }

    /**
     * Listing of franchise under leads.
     *
     * @return \Illuminate\Http\Response
     */
    public function franchiseInLeads()
    {
        $leads = $this->leadTransferRepository->getFranchiseInLeads();
        return $this->showAll($leads);
    }

    /**
     * Listing of franchise under leads.
     *
     * @return \Illuminate\Http\Response
     */
    public function leadsNewFranchise()
    {
        $leads = $this->leadTransferRepository->getFranchiseInLeads();
        return $this->showAll($leads);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
        //
    }
}
