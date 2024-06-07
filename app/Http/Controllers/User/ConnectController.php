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
        private readonly FacebookService $importFacebook,
        private readonly LinkedInService $importLinkedin,
    ) {
        $this->facebookService = $importFacebook;
        $this->linkedinService = $importLinkedin;
    }

    public function index()
    {
        $platforms = [
            [
                'image' => 'assets/images/icons/icons-facebook.png',
                'text' => 'Connect With Facebook',
                'is_connected' => (Auth::guard('web')->user()->meta_access_token != null) ? 1 : 0,
                'connect_route' => 'user.connect.facebook',
                'disconnect_route' => 'user.connect.facebook.disconnect',
                'permission' => 'connect_facebook',
                'user_avatar' => (Auth::guard('web')->user()->meta_access_token != null) ? Auth::guard('web')->user()->meta_avatar : null,
                'user_name' => (Auth::guard('web')->user()->meta_access_token != null) ? Auth::guard('web')->user()->meta_name : null,
                'user_email' => (Auth::guard('web')->user()->meta_access_token != null) ? Auth::guard('web')->user()->meta_email : null,
            ],
            [
                'image' => 'assets/images/icons/icons-linkedin.png',
                'text' => 'Connect With LinkedIn',
                'is_connected' => (Auth::guard('web')->user()->linkedin_access_token != null) ? 1 : 0,
                'connect_route' => 'user.connect.linkedin',
                'disconnect_route' => 'user.connect.linkedin.disconnect',
                'permission' => 'connect_linkedin',
                'user_avatar' => (Auth::guard('web')->user()->linkedin_access_token != null) ? Auth::guard('web')->user()->linkedin_avatar : null,
                'user_name' => (Auth::guard('web')->user()->linkedin_access_token != null) ? Auth::guard('web')->user()->linkedin_name : null,
                'user_email' => (Auth::guard('web')->user()->linkedin_access_token != null) ? Auth::guard('web')->user()->linkedin_email : null,
            ]
        ];
        return view('user.connect.index', compact('platforms'));
    }

    /**
     * Facebook
     */
    public function facebook()
    {
        return Socialite::driver('facebook')
            ->scopes([
                'email',
                'public_profile',
                'pages_show_list',
                'pages_read_engagement',
                'pages_manage_posts',
                'instagram_basic',
                'instagram_content_publish',
                'instagram_manage_media',
            ])
            ->redirect();
    }

    public function facebookCallback(Request $request)
    {
        try {
            $user = Socialite::driver('facebook')->user();

            $response = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'avatar' => $user->getAvatar(),
                'token' => $this->facebookService->tokenTime($user->token),
            ];

            $user = User::where('id', Auth::guard('web')->user()->id)->where('meta_email', $response['email'])->first();

            if (!$user) throw new Exception('Sorry this facebook account is not register with us.');

            $user->meta_access_token = $response['token'];
            $user->meta_name = $response['name'];
            $user->meta_avatar = $response['avatar'];
            $user->save();

            Session::flash('success', ['text' => 'Your Facebook account connected successfully.']);
            return redirect()->route('user.connect');
        } catch (Exception $e) {
            Session::flash('error', ['text' => $e->getMessage()]);
            return redirect()->route('user.connect');
        }
    }

    public function facebookDisconnect()
    {
        $user = User::where('id', Auth::guard('web')->user()->id)->first();
        $user->meta_access_token = null;
        $user->meta_avatar = null;
        $user->meta_name = null;
        $user->save();

        Session::flash('success', ['text' => 'Your Facebook account disconnected.']);
        return redirect()->route('user.connect');
    }



    /**
     * Linkedin
     */
    public function linkedin()
    {
        return redirect($this->linkedinService->generateAuthUrl());
    }

    public function linkedinCallback(Request $request)
    {
        try {
            $code = $request->code;

            $generateAccessToken = $this->linkedinService->generateAccessToken($code);
            if ($generateAccessToken === false) throw new Exception('Failed to generate access token');

            $getProfile = $this->linkedinService->getProfile($generateAccessToken);
            if ($getProfile === false) throw new Exception('Failed to get profile');

            $user = User::where('id', Auth::guard('web')->user()->id)->where('linkedin_email', $getProfile['email'])->first();

            if (!$user) {
                Session::flash('error', ['text' => 'Sorry this linkedin account is not register with us.']);
                return redirect()->route('user.connect');
            }

            $user->linkedin_access_token = $generateAccessToken;
            $user->linkedin_urn = $getProfile['sub'];
            $user->linkedin_name = $getProfile['name'];
            $user->linkedin_avatar = $getProfile['picture'];
            $user->save();

            return redirect($this->linkedinService->generateAuthUrl2());

            Session::flash('success', ['text' => 'Your Linkedin account connected successfully.']);
            return redirect()->route('user.connect');
        } catch (Exception $e) {
            Session::flash('error', ['text' => 'Something went wrong. Please try again.']);
            return redirect()->route('user.connect');
        }
    }

    public function linkedinCallback2(Request $request)
    {
        try {
            $code = $request->code;

            $generateAccessToken = $this->linkedinService->generateAccessToken2($code);
            if ($generateAccessToken === false) throw new Exception('Failed to generate access token');

            $user = User::where('id', Auth::guard('web')->user()->id)->first();
            $user->linkedin_community_access_token = $generateAccessToken;
            $user->save();

            Session::flash('success', ['text' => 'Your Linkedin account connected successfully.']);
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
        $user->linkedin_community_access_token = null;
        $user->linkedin_urn = null;
        $user->linkedin_avatar = null;
        $user->linkedin_name = null;
        $user->save();

        Session::flash('success', ['text' => 'Your Linkedin account disconnected successfully.']);
        return redirect()->route('user.connect');
    }
}
