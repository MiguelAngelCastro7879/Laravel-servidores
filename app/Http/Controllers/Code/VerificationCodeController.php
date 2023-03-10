<?php

namespace App\Http\Controllers\Code;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Mail\VerificationCodeMailer;
use Illuminate\Support\Facades\Mail;
use App\Models\VerificationCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class VerificationCodeController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $codigoLogin = '';
        $codigoVerificación = '';
        for ($i = 0; $i < 6; $i++) {
            $codigoLogin .= mt_rand(0, 9);
            $codigoVerificación .= mt_rand(0, 9);
        }
        
        $code_gen = new VerificationCode();
        $code_gen->user_id = Auth::user()->id;
        $code_gen->login_code = Hash::make($codigoLogin);
        $code_gen->login_code_confirmation = Crypt::encryptString($codigoLogin);
        $code_gen->verify_code = Hash::make($codigoVerificación);
        $code_gen->verify_code_confirmation = Crypt::encryptString($codigoVerificación);
        $code_gen->save();

        $signed_url = URL::temporarySignedRoute(
            'show_code', now()->addMinutes(30), Auth::user()->id
        );

        $mail= new VerificationCodeMailer($signed_url);
        Mail::to(Auth::user()->email)
            ->send($mail);
        return view('code.verify_code');
    }

    public function show(Request $request)
    {
        $code = VerificationCode::where('user_id', Auth::user()->id)->first();
        return view('code.show_code',['code'=>Crypt::decryptString($code->verify_code_confirmation)]);
    }
    
    public function validate_code_login(Request $request)
    {
        $login_code = $request->input('login_code');
        $user_codes = VerificationCode::where('user_id', Auth::user()->id)
            ->where('status',true)
            ->get();
        foreach ($user_codes as $codes) {
            if(Hash::check($login_code, $codes->login_code)){
                Session::put('code', $codes->login_code);
                return redirect('dashboard');
            }
        }
        return view('code.verify_code');
    }
    
    public function validate_code_application(Request $request)
    {
        $application_code = $request->input('application_code');
        $user_codes = VerificationCode::where('status', true)->get();
        
        foreach ($user_codes as $codes) {
            if(Hash::check($application_code, $codes->verify_code)){
                // error_log("buen codigo bro");
                return response()->json([
                    'login_code'=> Crypt::decryptString($codes->login_code_confirmation)
                ],201);
            }
        }
        return response()->json([
            'message'=> "invalid Code"
        ], 406);
    }

}
