<?php

namespace App\Services;

use App\Models\User;
use CURLFile;
use Exception;
use Illuminate\Support\Facades\Auth;

class InstagramService
{
    protected $baseUrl;
    protected $version;

    protected $ig_user_id;
    protected $access_token;

    public function __construct()
    {
        $this->baseUrl = 'https://graph.facebook.com';
        $this->version = env('FACEBOOK_GRAPH_VERSION');
    }

    public function setAccessToken($ig_user_id, $user_id = null)
    {
        $this->ig_user_id = $ig_user_id;

        if ($user_id == null) {
            $user = Auth::guard('web')->user();
            $this->access_token = $user->meta_access_token;
        } else {
            $user = User::find($user_id);
            $this->access_token = $user->meta_access_token;
        }
    }


    /**
     * Get Instagram Account from page
     */
    public function getInstagramAccount($page_id, $page_access_token, $user_id = null)
    {
        $params = [
            'fields' => 'instagram_business_account',
            'access_token' => $page_access_token
        ];

        $url = "$this->baseUrl/$this->version/$page_id?" . http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false,);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        $decodeResponse = json_decode($response, true);
        if (isset($decodeResponse['instagram_business_account'])) {
            return $decodeResponse['instagram_business_account']['id'];
        } else {
            return null;
        }
    }

    /**
     * Post
     * Text
     * Image
     * Video
     */

    /**
     * Image
     */
    public function postImage($ig_user_id, $imagePath, $title, $user_id = null)
    {
        try {
            $this->setAccessToken($ig_user_id, $user_id);

            $url = "$this->baseUrl/$this->version/$ig_user_id/media";

            $params = [
                'image_url' => $imagePath,
                'caption' => $title,
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $this->access_token]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($response, true);

            if (isset($data['error'])) throw new Exception($data['error']['message']);

            $creation_id = $data['id'];

            return $this->postImage2($creation_id);
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function postImage2($creation_id)
    {
        try {
            $url = "$this->baseUrl/$this->version/$this->ig_user_id/media_publish";

            $params = [
                'creation_id' => $creation_id,
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $this->access_token]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);

            if (isset($data['error'])) throw new Exception($data['error']['message']);

            return [
                'status' => 200,
                'data' => json_decode($response, true),
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }




    /**
     * Video
     */
    public function postVideo($ig_user_id, $media, $mediaSize, $title, $user_id = null)
    {
        try {
            $this->setAccessToken($ig_user_id, $user_id);

            $url = "$this->baseUrl/$this->version/$ig_user_id/media";

            $params = [
                'media_type' => 'REELS',
                'video_url' => $media,
                'caption' => $title,
                'share_to_feed' => true,
                'access_token' => $this->access_token,
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $this->access_token]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($response, true);

            if (isset($data['error'])) throw new Exception($data['error']['message']);

            $creation_id = $data['id'];

            return $this->postVideo2($creation_id, $media, $mediaSize);
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function postVideo2($creation_id, $media, $mediaSize)
    {
        try {
            $url = "https://rupload.facebook.com/ig-api-upload/$this->version/$creation_id/media_publish";

            $headers = [
                'Authorization: Bearer ' . $this->access_token,
                'Content-Type: application/octet-stream',
                'Content-Length: ' . $mediaSize,
                'Offset: 0',
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($media));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);

            if (isset($data['error'])) throw new Exception($data['error']['message']);

            return [
                'status' => 200,
                'data' => json_decode($response, true),
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }
}
