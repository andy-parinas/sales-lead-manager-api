<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Reports\Interfaces\RoofSheetProfileReport;
use App\Traits\RoofSheetProfileComputer;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoofSheetProfileReportController extends ApiController
{
    use RoofSheetProfileComputer;

    private $roofSheetProfileReport;

    public function __construct(RoofSheetProfileReport $roofSheetProfileReport)
    {
        $this->middleware('auth:sanctum');
        $this->roofSheetProfileReport = $roofSheetProfileReport;
    }

    public function __invoke(Request $request)
    {
        $user = Auth::user();
        
        if($request->has('start_date') && $request->has('end_date')){

            $results = [];
            
            if($user->user_type == User::HEAD_OFFICE){
                $results = $this->roofSheetProfileReport->generate($request->all());
            }else {
                $franchiseIds = $user->franchises->pluck('id')->toArray();
                $results = $this->roofSheetProfileReport->generateByFranchise($franchiseIds, $request->all());
            }
            
            if($results->count() > 0){
                $total = $this->computeTotal($results);
                return $this->showOne([
                    'results' => $results,
                    'total' => $total
                ]);
            }
            
            return $this->showOne([
                'results' => $results
            ]);
        }
    }
}
