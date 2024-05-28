<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use App\Services\FacebookService;
use App\Services\LinkedInService;

class ConnectController extends Controller
{
    protected $linkedinService;
    protected $facebookService;

    public function __construct(
        private readonly LinkedInService $importLinkedin,
        private readonly FacebookService $importService
    )
    {
        $this->linkedinService = $importLinkedin;
        $this->facebookService = $importService;
    }

    public function index()
    {
        return view('user.connect.index');
    }

    public function facebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function facebookCallback(Request $request)
    {
        try {
            $user = Socialite::driver('facebook')->user();

            $userData = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'avatar' => $user->getAvatar(),
                'token' => $this->facebookService->tokenTime($user->token),
            ];

            return response()->json($userData);

            $user = User::where('id', Auth::guard('web')->user()->id)->first();
            $user->meta_email = $userData['email'];
            $user->meta_access_token = $userData['token'];
            $user->save();

            return redirect()->route('user.connect');
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
            $code = $request->code;

            $generateAccessToken = $this->linkedinService->generateAccessToken($code);
            if ($generateAccessToken === false) throw new Exception('Failed to generate access token');

            $getProfile = $this->linkedinService->getProfile();
            if ($getProfile === false) throw new Exception('Failed to get profile');

            $user = User::where('id', Auth::guard('web')->user()->id)->first();
            $user->linkedin_access_token = $generateAccessToken;
            $user->linkedin_urn = $getProfile['sub'];
            $user->linkedin_name = $getProfile['name'];
            $user->linkedin_avatar = $getProfile['picture'];
            $user->linkedin_email = $getProfile['email'];
            $user->save();

            return redirect()->route('user.connect');
        } catch (Exception $e) {
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
