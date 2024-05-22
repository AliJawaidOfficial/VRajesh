<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

class ConnectController extends Controller
{
    public function index()
    {
        return view('user.connect.index');
    }

    public function facebook()
    {
        return Socialite::driver('facebook')
            // ->scopes(['email', 'public_profile'])
            ->redirect();
    }

    public function facebookCallback()
    {
        try {
            $user = Socialite::driver('facebook')->user();
            Session::put('user', $user);
            return redirect('/');
        } catch (Exception $e) {
            Session::flash('error', ['text' => $e->getMessage()]);
            return redirect()->route('dashboard');
        }
    }

    public function linkedin()
    {
        $param = [
            'response_type' => 'code',
            'client_id' => env('LINKEDIN_CLIENT_ID'),
            'scope' => 'openid profile email w_member_social ',
            'redirect_uri' => env('LINKEDIN_CALLBACK_URL'),
            'state' => uniqid(),
        ];
        $url = 'https://www.linkedin.com/oauth/v2/authorization?' . http_build_query($param);
        return redirect($url);
    }

    public function linkedinCallback(Request $request)
    {
        try {
            $code = $request->code;

            $url = 'https://www.linkedin.com/oauth/v2/accessToken';
            $params = [
                'grant_type' => 'authorization_code',
                'client_id' => env('LINKEDIN_CLIENT_ID'),
                'client_secret' => env('LINKEDIN_CLIENT_SECRET'),
                'redirect_uri' => env('LINKEDIN_CALLBACK_URL'),
                'code' => $code,
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            if ($response === false) throw new Exception(curl_error($ch));
            curl_close($ch);
            $data = json_decode($response, true);

            Session::put('linkedin_access_token', $data['access_token']);

            // User Info
            $url = 'https://api.linkedin.com/v2/userinfo';
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $data['access_token'],
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            if ($response === false) throw new Exception(curl_error($ch));
            curl_close($ch);
            $data = json_decode($response, true);

            Session::put('linkedin_user', $data);

            return redirect()->route('user.connect');
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function logout() {
        Session::flush();
        return redirect()->route('user.connect');
    }
}
