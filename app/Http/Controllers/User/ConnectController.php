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
use App\Services\GoogleService;
use App\Services\LinkedInService;

class ConnectController extends Controller
{
    protected $facebookService;
    protected $googleService;
    protected $linkedinService;

    public function __construct(
        private readonly FacebookService $importFacebook,
        private readonly GoogleService $importGoogle,
        private readonly LinkedInService $importLinkedin,
    ) {
        $this->facebookService = $importFacebook;
        $this->googleService = $importGoogle;
        $this->linkedinService = $importLinkedin;
    }

    public function index()
    {
        $user = Auth::guard('web')->user();
        $platforms = [
            [
                'image' => 'assets/images/icons/icons-facebook.png',
                'text' => 'Connect With Facebook',
                'is_connected' => ($user->meta_access_token != null) ? 1 : 0,
                'connect_route' => 'user.connect.facebook',
                'disconnect_route' => 'user.connect.facebook.disconnect',
                'permission' => 'connect_facebook',
                'user_avatar' => ($user->meta_access_token != null) ? $user->meta_avatar : null,
                'user_name' => ($user->meta_access_token != null) ? $user->meta_name : null,
                'user_email' => ($user->meta_access_token != null) ? $user->meta_email : null,
            ],
            [
                'image' => 'assets/images/icons/icons-linkedin.png',
                'text' => 'Connect With LinkedIn',
                'is_connected' => ($user->linkedin_access_token != null && $user->linkedin_community_access_token != null) ? 1 : 0,
                'connect_route' => 'user.connect.linkedin',
                'disconnect_route' => 'user.connect.linkedin.disconnect',
                'permission' => 'connect_linkedin',
                'user_avatar' => ($user->linkedin_access_token != null) ? $user->linkedin_avatar : null,
                'user_name' => ($user->linkedin_access_token != null) ? $user->linkedin_name : null,
                'user_email' => ($user->linkedin_access_token != null) ? $user->linkedin_email : null,
            ],
            [
                'image' => 'assets/images/icons/icons-google.png',
                'text' => 'Connect With Google',
                'is_connected' => ($user->google_access_token != null) ? 1 : 0,
                'connect_route' => 'user.connect.google',
                'disconnect_route' => 'user.connect.google.disconnect',
                'permission' => 'connect_google',
                'user_avatar' => ($user->google_access_token != null) ? $user->google_avatar : null,
                'user_name' => ($user->google_access_token != null) ? $user->google_name : null,
                'user_email' => ($user->google_access_token != null) ? $user->google_email : null,
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
                'publish_video',
                'email',
                'public_profile',
                'pages_show_list',
                'business_management',
                'pages_read_engagement',
                'pages_manage_posts',
                'instagram_basic',
                'instagram_content_publish',
                // 'instagram_manage_media',
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
     * Google
     */
    public function google()
    {
        return Socialite::driver('google')
            ->scopes([
                'https://www.googleapis.com/auth/business.manage'
            ])
            ->redirect();
    }

    public function googleCallback(Request $request)
    {
        try {
            $user = Socialite::driver('google')->user();

            $response = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'avatar' => $user->getAvatar(),
                'token' => $user->token,
                'refresh_token' => $user->refreshToken,
                'expires_in' => $user->expiresIn,
            ];

            return $response;

            $user = User::where('id', Auth::guard('web')->user()->id)->where('google_email', $response['email'])->first();
            if (!$user) throw new Exception('Sorry this google account is not register with us.');

            $user->google_access_token = $response['token'];
            $user->google_refresh_token = $response['refresh_token'];
            $user->google_token_expires_at = now()->addSeconds($response['expires_in']);

            $user->google_avatar = $response['avatar'];
            $user->google_name = $response['name'];
            $user->save();

            Session::flash('success', ['text' => 'Your Google account connected successfully.']);
            return redirect()->route('user.connect');
        } catch (Exception $e) {
            Session::flash('error', ['text' => $e->getMessage()]);
            return redirect()->route('user.connect');
        }
    }

    public function googleDisconnect()
    {
        $user = User::where('id', Auth::guard('web')->user()->id)->first();
        $user->google_access_token = null;
        $user->google_avatar = null;
        $user->google_name = null;
        $user->save();

        Session::flash('success', ['text' => 'Your Google account disconnected.']);
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
