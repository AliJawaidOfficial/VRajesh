<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use CURLFile;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FacebookService
{
    protected $baseUrl;
    protected $version;

    protected $appId;
    protected $appSecret;

    protected $accessToken;

    protected $pageId;
    protected $pageAccessToken;

    public function __construct()
    {
        $this->baseUrl = 'https://graph-video.facebook.com';
        $this->version = env('FACEBOOK_GRAPH_VERSION');
        $this->appId = env('FACEBOOK_CLIENT_ID');
        $this->appSecret = env('FACEBOOK_CLIENT_SECRET');
    }

    public function setAccessToken($user_id = null)
    {
        if ($user_id === null) {
            $this->accessToken = Auth::guard('web')->user()->meta_access_token;
        } else {
            $user = User::where('id', $user_id)->first();
            $this->accessToken = $user->meta_access_token;
        }
    }


    /**
     * Token Time
     */
    public function tokenTime($token, $user_id = null)
    {
        try {
            $param = [
                "grant_type" => "fb_exchange_token",
                "client_id" => $this->appId,
                "client_secret" => $this->appSecret,
                "fb_exchange_token" => $token
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "$this->baseUrl/oauth/access_token?" . http_build_query($param));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);

            $data = json_decode($response, true);
            return $data['access_token'];

            if (isset($response['error'])) throw new Exception($response['error']['message']);

            return;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


    /**
     * Get Pages
     */
    public function getPages($user_id = null)
    {
        $this->setAccessToken($user_id);

        $url = "$this->baseUrl/$this->version/me/accounts?" . http_build_query([
            'access_token' => $this->accessToken,
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false,);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error != null) return $error;
        $decodeResponse = json_decode($response, true);
        return $decodeResponse['data'];
    }



    /**
     * Post
     * Text
     * Video
     */

    /**
     * Text
     */
    public function postText($page_id, $page_access_token, $text, $user_id = null)
    {
        try {
            $this->pageId = $page_id;
            $this->pageAccessToken = $page_access_token;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "$this->baseUrl/$this->version/$this->pageId/feed");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                "message" => $text,
                "access_token" => $this->pageAccessToken,
                "published" => true,
            ]));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);

            return $response;

            if (isset($response['error'])) throw new Exception($response['error']['message']);

            return;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $e->getMessage();
        }
    }

    /**
     * Image
     */
    public function postImages($page_id, $page_access_token, $images, $title, $user_id = null)
    {
        try {
            $this->pageId = $page_id;
            $this->pageAccessToken = $page_access_token;

            $this->setAccessToken($user_id);

            $attachedMedia = [];

            foreach (explode(',', $images) as $index => $image) {

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "$this->baseUrl/$this->version/$this->pageId/photos");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, [
                    "access_token" => $this->pageAccessToken,
                    "source" => new CURLFile($image),
                    "published" => false,
                ]);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: multipart/form-data',
                ]);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode != 200) Log::error($response['error']);

                $data = json_decode($response, true);
                $attachedMedia["attached_media[$index]"] = json_encode(["media_fbid" => $data['id']]);
            }

            return $this->postImages2($attachedMedia, $title);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }
    }

    public function postImages2($attachedMedia, $title)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "$this->baseUrl/$this->version/$this->pageId/feed");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, array_merge([
                "access_token" => $this->pageAccessToken,
                "message" => $title,
                "published" => true,
            ], $attachedMedia));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: multipart/form-data',
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);

            return [
                'status' => 200,
                'data' => $response
            ];
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }
    }



    /**
     * Video
     */
    public function postVideo($page_id, $page_access_token, $videoSizes, $video, $title, $user_id = null)
    {
        try {
            $this->pageId = $page_id;
            $this->pageAccessToken = $page_access_token;

            $videoPath = env('APP_URL') . '/' . $video;

            // Step 1
            $response = $this->postVideo1($videoSizes);
            if (isset($response['error'])) throw new Exception($response['error']['message']);

            $responseData = json_decode($response, true);
            $uploadSessionId = $responseData['upload_session_id'];

            // Step 2
            $step2response = $this->postVideo2($uploadSessionId, $videoPath);
            if (isset($step2response['error'])) throw new Exception($step2response['error']['message']);

            // Step 3
            $step3response = $this->postVideo3($uploadSessionId, $title);
            if (isset($step3response['error'])) throw new Exception($step3response['error']['message']);

            return $step3response;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $e->getMessage();
        }
    }


    public function postVideo1($fileSize)
    {
        $url = "$this->baseUrl/$this->version/$this->pageId/videos";
        $postFields = [
            'upload_phase' => 'start',
            'access_token' => $this->pageAccessToken,
            'file_size' => $fileSize,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false,);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }


    public function postVideo2($uploadSessionId, $file)
    {
        $url = "$this->baseUrl/$this->version/$this->pageId/videos";
        $postFields = [
            'upload_phase' => 'transfer',
            'access_token' => $this->pageAccessToken,
            'upload_session_id' => $uploadSessionId,
            'start_offset' => 0,
            'video_file_chunk' => new CURLFile($file),
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false,);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }


    public function postVideo3($uploadSessionId, $title)
    {
        $url = "$this->baseUrl/$this->version/$this->pageId/videos";
        $postFields = [
            'upload_phase' => 'finish',
            'access_token' => $this->pageAccessToken,
            'upload_session_id' => $uploadSessionId,
            'description' => $title,
            'published' => 'true',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false,);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}
