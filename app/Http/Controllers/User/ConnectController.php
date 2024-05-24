<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\LinkedInService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

class ConnectController extends Controller
{
    protected $linkedinService;

    public function __construct(private readonly LinkedInService $service)
    {
        $this->linkedinService = $service;
    }

    public function index()
    {
        return view('user.connect.index');
    }

    public function facebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function facebookCallback()
    {
        try {
            $user = Socialite::driver('facebook')->user();
            return $user;
            Session::put('user', $user);
            return redirect('/');
        } catch (Exception $e) {
            Session::flash('error', ['text' => $e->getMessage()]);
            return redirect()->route('user.connect');
        }
    }

    public function facebookDisconnect()
    {
        $user = User::where('id', Auth::guard('web')->user()->id)->first();
        $user->facebook_access_token = null;
        $user->save();
        return redirect()->route('user.connect');
    }

    public function linkedin()
    {
        // return Socialite::driver('linkedin-openid')->redirect();
        return redirect($this->linkedinService->generateAuthUrl());
    }

    public function linkedinCallback(Request $request)
    {
        try {
            // return Socialite::driver('linkedin-openid')->user();

            $code = $request->code;

            $generateAccessToken = $this->linkedinService->generateAccessToken($code);
            if ($generateAccessToken === false) throw new Exception('Failed to generate access token');

            $getProfile = $this->linkedinService->getProfile();
            if ($getProfile === false) throw new Exception('Failed to get profile');

            $user = User::where('id', Auth::guard('web')->user()->id)->first();
            $user->linkedin_access_token = $generateAccessToken;
            $user->linkedin_urn = $getProfile['sub'];
            $user->save();

            return redirect()->route('user.connect');
        } catch (Exception $e) {
            return $e->getMessage();
            Session::flash('error', ['text' => 'Something went wrong. Please try again.']);
            return redirect()->route('user.connect');
        }
    }

    public function linkedinDisconnect()
    {
        $user = User::where('id', Auth::guard('web')->user()->id)->first();
        $user->linkedin_access_token = null;
        $user->linkedin_urn = null;
        $user->save();
        return redirect()->route('user.connect');
    }

    public function logout()
    {
        Session::flush();
        return redirect()->route('user.connect');
    }
}
