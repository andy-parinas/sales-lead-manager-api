<?php

namespace App\Http\Controllers\Auth;

use App\OneTimePin;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Services\Interfaces\EmailServiceInterface;
use App\Http\Resources\Franchise as FranchiseResource;
use App\User;

class LoginController extends ApiController
{

    protected $emailService;

    public function __construct(EmailServiceInterface $emailService){
        $this->emailService = $emailService;
    }

//    use AuthenticatesUsers;
//
//    /**
//     * Where to redirect users after login.
//     *
//     * @var string
//     */
//    protected $redirectTo = RouteServiceProvider::HOME;
//
//    /**
//     * Create a new controller instance.
//     *
//     * @return void
//     */
//    public function __construct()
//    {
//        $this->middleware('guest')->except('logout');
//    }
//
//    public function username()
//    {
//        return 'username';
//    }

    public function login(Request $request){
        $loginData = $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);

        if(Auth::attempt($loginData)){
            $user = Auth::user();

            $franchises = $user->franchises;
            return response()->json(['data' => Auth::user(), 'franchises' => FranchiseResource::collection($franchises) ], Response::HTTP_OK);
            
            // OneTimePin::where('user_id', $user->id)->delete();

            // $otp = rand(100000, 999999);

            // $expiredAt = date('Y-m-d H:i:s', strtotime("+5 min"));

            // $oneTimePin = new OneTimePin();
            // $oneTimePin->code = $otp;
            // $oneTimePin->expired_at = $expiredAt;
            // $oneTimePin->user_id = $user->id;
            // $oneTimePin->save();
            
            // $this->sendOtpEmail($user->email, $otp);
            
            // return response()->json(null, Response::HTTP_OK);
        }

        return $this->errorResponse("Invalid Username or Password", Response::HTTP_UNAUTHORIZED);
    }

    public function logout(Request $request){

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response('', Response::HTTP_NO_CONTENT);

        //Auth::guard('web')->logout();
    }

    public function resendOtp(){
        $user = Auth::user();

        $oneTimePin = OneTimePin::where('user_id', $user->id)->first();
        
        if($oneTimePin){
            $oneTimePin->delete();
        }

        $otp = rand(100000, 999999);

        $expiredAt = date('Y-m-d H:i:s', strtotime("+5 min"));

        $oneTimePin = new OneTimePin();
        $oneTimePin->code = $otp;
        $oneTimePin->expired_at = $expiredAt;
        $oneTimePin->user_id = $user->id;
        $oneTimePin->save();

        $this->sendOtpEmail($user->email, $otp);
        
        return response()->json(null, Response::HTTP_OK);
    }

    public function verifyOtp(Request $request){

        $otp = $request->input('otp');
        $user = Auth::user();
        
        $oneTimePin = OneTimePin::where('user_id', $user->id)
            ->where('code', $otp)
            ->where('expired_at', '<=', date('Y-m-d H:i:s', strtotime("+5 min")))
            ->first();
        if($oneTimePin){
            $oneTimePin->delete();
            $franchises = $user->franchises;
            return response()->json(['data' => Auth::user(), 'franchises' => FranchiseResource::collection($user->franchises) ], Response::HTTP_OK);
        }else{
            return $this->errorResponse("Invalid OTP", Response::HTTP_UNAUTHORIZED);
        }
    }

    public function sendOtpEmail($to, $otp)
    {
        $from = 'support@spanline.com.au';
        $subject = "Ezi-Task CRM - One Time Pin";

        $message = view('emails.otp')->with([
            'otp' => $otp
        ])->render();

        $this->emailService->sendEmail($to, $from, $subject, $message);
    }

    public function getAllUserEmail(){
        $users = User::all();
        $emails = [];
        foreach($users as $user){
            $emails[] = $user->name.'-'.$user->email;
        }
        return $emails;
    }
}
