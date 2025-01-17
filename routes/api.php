<?php

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


/**
 * Authentication Test Route
 */
Route::get('auth-test', 'HomeController@index');

/**
 * Authentication Route
 */
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout');

Route::post('verify', 'Auth\LoginController@verifyOtp');
Route::post('resend', 'Auth\LoginController@resendOtp');
Route::get('useremail', 'Auth\LoginController@getAllUserEmail');
/**
 * Franchise Route
 */
Route::post('franchises/uploads', 'Franchise\FranchisePostcodeUploadController');
Route::get('franchises/parents', 'Franchise\FranchiseController@parents');
Route::get('franchises/sub-franchises', 'Franchise\FranchiseController@subFranchise');

Route::resource('franchises', 'Franchise\FranchiseController', ['except' => ['create', 'edit']]);

Route::resource('franchises.children', 'Franchise\FranchiseChildrenController', ['only' => ['index', 'store']]);
Route::resource('franchises.leads', 'Franchise\FranchiseLeadController', ['except' => ['create', 'edit']]);
Route::put('franchises/{franchise}/leads/{lead}/franchise', 'Franchise\FranchiseLeadFranchiseController@update');
Route::get('franchises/{franchise}/related', 'Franchise\FranchiseController@related');

Route::get('franchises/{franchise}/postcodes/available', 'Franchise\FranchisePostcodeController@available');
Route::post('franchises/{franchise}/postcodes/detach', 'Franchise\FranchisePostcodeController@detach');

Route::resource('franchises.postcodes', 'Franchise\FranchisePostcodeController', ['only' => ['index', 'store', 'destroy']]);

Route::post('franchises/{franchise}/postcodes/{postcode}/attach', 'Franchise\FranchisePostcodeController@attach');
Route::get('franchises/{franchise}/postcodes/{postcode}/check', 'Franchise\FranchisePostcodeController@check');



/**
 * Lead Route
 */
Route::resource('leads.job-types', 'Lead\LeadJobTypeController', ['only' => ['update', 'store']]);
Route::resource('leads.appointments', 'Lead\LeadAppointmentController', ['only' => ['update']]);
Route::resource('leads.documents', 'Lead\LeadDocumentController', ['only' => ['index', 'show', 'store', 'destroy']]);
Route::resource('leads.contracts', 'Lead\LeadContractController', ['except' => ['create', 'edit']]);
Route::resource('leads.finances', 'Lead\LeadFinanceController', ['except' => ['create', 'edit']]);
Route::resource('leads.constructions', 'Lead\LeadConstructionController', ['except' => ['create', 'edit']]);
Route::resource('leads.authorities', 'Lead\LeadBuildingAuthorityController', ['except' => ['create', 'edit']]);
Route::resource('leads.verifications', 'Lead\LeadVerificationController', ['except' => ['create', 'edit']]);
Route::resource('leads.customer-reviews', 'Lead\LeadCustomerReviewController', ['except' => ['create', 'edit']]);
Route::resource('leads.warranty-claim', 'Lead\LeadWarrantyClaimController', ['except' => ['create', 'edit']]);
Route::resource('leads', 'Lead\LeadController', ['only' => ['index', 'show', 'destroy', 'update']]);

/**
 * Lead Transfer Route
 */
Route::get('leadstransfer/byfranchiseid/{franchiseid}', 'Lead\LeadTransferController@byFranchiseId');
Route::get('leadstransfer/franchiseinleads', 'Lead\LeadTransferController@franchiseInLeads');
Route::get('leadstransfer/totalfranchiseinleads/{franchiseid}', 'Lead\LeadTransferController@totalFranchiseInLeads');
Route::resource('leadstransfer', 'Lead\LeadTransferController', ['except' => ['create', 'edit']]);
/**
 * Warranty Claims/Complaints dropdowns
 */
Route::resource('warranty-claim-dropdown', 'Lead\LeadWarrantyClaimDropdownController', ['except' => ['edit', 'create', 'show']]);
Route::get('warranty-claim-dropdown/name/{name}', 'Lead\LeadWarrantyClaimDropdownController@dropdownText');

/**
 * Appointment dropdowns
 */
Route::resource('appointment-dropdown', 'Lead\LeadAppointmentDropdownController', ['except' => ['edit', 'create', 'show']]);
Route::get('appointment-dropdown/name/{name}', 'Lead\LeadAppointmentDropdownController@dropdownText');

/**
 * Roof Sheet dropdowns
 */
Route::resource('roof-sheet-dropdown', 'Lead\LeadRoofSheetDropdownController', ['except' => ['edit', 'create', 'show']]);

/**
 * Finance Route
 */
Route::resource('finances.payments-made', 'Finance\FinancePaymentMadeController',['except' => ['create', 'edit']] );
Route::resource('finances.payment-schedules', 'Finance\FinancePaymentScheduleController',['except' => ['create', 'edit']] );
Route::post('finances/{finance}/payment-schedules/{payment_schedule}/convert', 'Finance\FinancePaymentScheduleController@convert');
Route::post('finances/{finance}/payment-schedules/{payment_schedule}/pay', 'Finance\FinancePaymentScheduleController@pay');

/**
 * Lead Contract Route
 */
Route::resource('contracts.contract-variations', 'Contract\ContractVariationController', ['except' => ['edit', 'create']]);

/**
 *  User Routes
 */
Route::resource('users', 'User\UserController', ['except' => ['create', 'edit']]);
Route::resource('users.franchises', 'User\UserFranchiseController', ['only' => ['index', 'store', 'destroy']]);
Route::post('users/{user}/franchises/{franchise}', 'User\UserFranchiseController@attach');


/**
 *  SalesContact Routes
 */
Route::get('contacts/search', 'SalesContact\SalesContactController@search');
Route::resource('contacts', 'SalesContact\SalesContactController', ['except' => ['edit', 'create']]);


/**
 *  LeadSource Routes
 */
Route::resource('lead-sources', 'LeadSource\LeadSourceController', ['except' => ['edit', 'create', 'show']]);

/**
 * Product Routes
 */
Route::resource('products', 'Product\ProductController', ['except' => ['edit', 'create', 'show']]);

/**
 * TradeType Routes
 */
Route::resource('trade-types', 'TradeType\TradeTypeController', ['except' => ['edit', 'create', 'show']]);

/**
 * Postcode Routes
 */
Route::get('postcodes/search', 'Postcode\PostcodeController@search');
Route::resource('postcodes', 'Postcode\PostcodeController', ['only' => ['index', 'show', 'store']]);


/**
 * DesignAssessor Routes
 */
Route::resource('design-assessors', 'DesignAssessor\DesignAssessorController', ['except' => ['create', 'edit', 'show']]);

/**
 * SalesStaff Routes
 */
Route::get('active-sales-staffs/active-search', 'SalesStaff\SalesStaffController@activeSearch');
Route::get('sales-staffs/search', 'SalesStaff\SalesStaffController@search');
Route::get('sales-staffs/getall', 'SalesStaff\SalesStaffController@allSalesStaff');
Route::resource('sales-staffs', 'SalesStaff\SalesStaffController', ['except' => ['create', 'edit']]);
Route::post('sales-staffs/{salesStaffId}/franchises/{franchiseId}', 'SalesStaff\SalesStaffController@attachFranchise');
Route::post('sales-staffs/{salesStaffId}/franchises/{franchiseNumber}/detach', 'SalesStaff\SalesStaffController@detachFranchise');

/**
 * TradeStaff Routes
 */
Route::get('trade-staffs/search', 'TradeStaff\TradeStaffController@search');
Route::resource('trade-staffs', 'TradeStaff\TradeStaffController',['except' => ['create', 'edit']] );
Route::resource('trade-staffs.schedules', 'TradeStaff\TradeStaffScheduleController',['except' => ['create', 'edit', 'store']]);

/**
 * Report Routes
 */
Route::get('reports/sales-staff-lead-summary', 'Reports\SalesStaffLeadSummaryReportController@index');
Route::get('reports/sales-staff-summary', 'Reports\SalesStaffSummaryReportController@index');
Route::get('reports/sales-staff-product-summary', 'Reports\SalesStaffProductSummaryReportController@index');
Route::get('reports/sales-lead-appointment', 'Reports\AppointmentSummaryReportController@index');
Route::get('reports/product-sales-summary', 'Reports\ProductSalesSummaryReportController@index');
Route::get('reports/outcome', 'Reports\OutcomeSummaryReportController');
Route::get('reports/contract-outcome', 'Reports\ContractOutcomeSummaryReportController');
Route::get('reports/outcome-sales-staff', 'Reports\OutcomeSalesStaffReportController');
Route::get('reports/lead-sources', 'Reports\LeadSourceReportController');
Route::get('reports/customer-reviews', 'Reports\CustomerReviewReportController');
Route::get('reports/sales-contract', 'Reports\SalesContractReportController');
Route::get('reports/roof-sheet-profile', 'Reports\RoofSheetProfileReportController');
Route::get('reports/sales-contract-variation', 'Reports\SalesContractVariationReportController');
Route::get('reports/lead-contract', 'Reports\LeadAndContractReportController');
Route::get('reports/lead-contract-date', 'Reports\LeadAndContractDateReportController@index');
Route::get('reports/combine-lead-contract-date', 'Reports\LeadAndContractDateReportController@leadAndContract');
Route::get('reports/design-advisor', 'Reports\LeadAndContractDateReportController@allDesignAdvisory');

/**
 * CSV Report Routes
 */
Route::get('reports/sales-lead-appointment-csv', 'Reports\AppointmentSummaryReportController@csvReport');
Route::get('reports/outcome-sales-staff-csv', 'Reports\OutcomeSalesStaffReportController@csvReport');
Route::get('reports/lead-sources-csv', 'Reports\LeadSourceReportController@csvReport');
Route::get('reports/customer-reviews-csv', 'Reports\CustomerReviewReportController@csvReport');
Route::get('reports/lead-contract-date-csv', 'Reports\LeadAndContractDateReportController@csvReport');
Route::get('reports/sales-staff-lead-summary-csv', 'Reports\SalesStaffLeadSummaryReportController@csvReport');
Route::get('reports/sales-staff-summary-csv', 'Reports\SalesStaffSummaryReportController@csvReport');
Route::get('reports/combine-lead-contract-date-csv', 'Reports\LeadAndContractDateReportController@leadContractCsvReport');
Route::get('reports/sales-staff-product-summary-csv', 'Reports\SalesStaffProductSummaryReportController@csvReport');
Route::get('reports/product-sales-summary-csv', 'Reports\ProductSalesSummaryReportController@csvReport');
Route::get('reports/sales-contract-csv', 'Reports\SalesContractReportController@csvReport');
Route::get('reports/sales-contract-variation-csv', 'Reports\SalesContractVariationReportController@csvReport');
Route::get('reports/roof-sheet-profile-csv', 'Reports\RoofSheetProfileReportController@csvReport');
Route::get('reports/lead-contract-csv', 'Reports\LeadAndContractReportController@csvReport');
/**
 * Build Log Routes
 */
Route::resource('constructions.build-logs', 'Construction\ConstructionBuildLogController', ['except' => ['create', 'edit']]);


/**
 * Roof Routes
 */
Route::resource('roof-sheets', 'Roof\RoofSheetController', ['except' => ['create', 'edit', 'show']] );
Route::resource('roof-colours', 'Roof\RoofColourController', ['except' => ['create', 'edit', 'show']] );


/**
 * Letter Route
 */
Route::get('leads/{lead}/lead-data', 'Letter\AssignedIntroLetterController@leadData');
Route::post('leads/{lead}/letters/unassigned-intro/{sales_contact}', 'Letter\UnassignedIntroLetterController@send');
Route::post('leads/{lead}/letters/custom-unassigned-intro', 'Letter\UnassignedIntroLetterController@customSend');
Route::post('leads/{lead}/letters/assigned-intro/{sales_contact}', 'Letter\AssignedIntroLetterController@send');
Route::post('leads/{lead}/letters/custom-assigned-intro', 'Letter\AssignedIntroLetterController@customSend');
Route::post('contracts/{lead}/letters/welcome/', 'Letter\WelcomeLetterController@send');
Route::post('contracts/{lead}/letters/custom-welcome/', 'Letter\WelcomeLetterController@customSend');
Route::post('authorities/{lead}/letters/council-intro', 'Letter\CouncilIntroLetterController@send');
Route::post('authorities/{lead}/letters/custom-council-intro', 'Letter\CouncilIntroLetterController@customSend');
Route::post('authorities/{lead}/letters/no-council', 'Letter\NoCouncilLetterController@send');
Route::post('authorities/{lead}/letters/custom-no-council', 'Letter\NoCouncilLetterController@customSend');
Route::post('authorities/{lead}/letters/out-of-council', 'Letter\OutOfCouncilLetterController@send');
Route::post('authorities/{lead}/letters/custom-out-of-council', 'Letter\OutOfCouncilLetterController@customSend');
Route::post('job-types/{lead}/email/{sales_staff}', 'JobType\JobTypeEmailController@send');
Route::post('job-types/{lead}/sms/{sales_staff}', 'JobType\JobTypeSmsController@send');
Route::post('customer-reviews/{customer_review}/letters/maintenance/{sales_contact}', 'Letter\MaintenanceLetterController@send');
Route::post('customer-reviews/{lead}/letters/custom-maintenace-letter', 'Letter\MaintenanceLetterController@customSend');


Route::post('sanctum/token', function (Request $request) {
    $request->validate([
        'username' => 'required',
        'password' => 'required'
    ]);

    $user = User::where('username', $request->username)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'username' => ['The provided credentials are incorrect.'],
        ]);
    }

    return $user->createToken('desktop')->plainTextToken;
});

//Manuals
Route::resource('manuals', 'ManualController', ['only' => ['index']]);
