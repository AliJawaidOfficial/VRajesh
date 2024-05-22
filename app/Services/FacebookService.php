<?php

namespace App\Services;

use CURLFile;
use Exception;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class FacebookService
{
    protected $baseUrl;
    protected $version;
    protected $appId;
    protected $appSecret;
    protected $pageId;
    protected $pageAccessToken;

    public function __construct()
    {
        $this->baseUrl = 'https://graph-video.facebook.com';
        $this->version = env('FACEBOOK_GRAPH_VERSION');
        $this->appId = env('FACEBOOK_CLIENT_ID');
        $this->appSecret = env('FACEBOOK_CLIENT_SECRET');
        $this->pageId = env('FACEBOOK_PAGE_ID');
        $this->pageAccessToken = env('FACEBOOK_PAGE_ACCESS_TOKEN');
    }

    /**
     * Post
     * Text
     * Video
     */

    /**
     * Text
     */
    public function postText($text)
    {
        try {
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
            return $e->getMessage();
        }
    }

    /**
     * Image
     */
    public function postImage($imagePath, $title)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "$this->baseUrl/$this->version/$this->pageId/photos");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                "access_token" => $this->pageAccessToken,
                "message" => $title,
                "source" => new CURLFile($imagePath),
                "published" => true,
            ]);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: multipart/form-data',
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);

            Session::flash('success', ['text' => 'Post created successfully']);
            return redirect()->back();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


    /**
     * Video
     */
    public function postVideo($videoSize, $videoPath, $title)
    {
        try {
            // Step 1
            $response = $this->postVideo1($videoSize);
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

        // Initialize cURL session
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

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false,);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute cURL request
        $response = curl_exec($ch);

        // Close cURL session
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

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false,);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute cURL request
        $response = curl_exec($ch);

        // Close cURL session
        curl_close($ch);

        return $response;
    }
}
