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

    protected $igUserId;
    protected $accessToken;

    public function __construct()
    {
        $this->baseUrl = 'https://graph.facebook.com';
        $this->version = env('FACEBOOK_GRAPH_VERSION');
    }

    public function init($ig_user_id, $user_id = null)
    {
        $this->igUserId = $ig_user_id;

        if ($user_id == null) {
            $user = Auth::guard('web')->user();
            $this->accessToken = $user->meta_access_token;
        } else {
            $user = User::where('id', $user_id)->first();
            $this->accessToken = $user->meta_access_token;
        }
    }



    /**
     * Get Pages
     */
    public function getPages($user_id = null)
    {
        try {
            $this->init($user_id);

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

            $data = [];
            foreach ($decodeResponse['data'] as $page) {
                $igUserId = $this->getInstagramAccount($page['id'], $page['access_token'], $user_id);

                if ($igUserId != null) $data[] = [
                    'name' => $page['name'],
                    'id' => $page['id'],
                    'access_token' => $page['access_token'],
                    'ig_business_account' => $igUserId,
                ];
            }

            return $data;
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

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
    public function postImage($ig_user_id, $images, $text, $user_id = null)
    {
        try {
            $this->init($ig_user_id, $user_id);

            $url = "$this->baseUrl/$this->version/$ig_user_id/media";

            $errors = [];
            $creationIds = [];
            foreach (explode(',', $images) as $image) {
                $params = [
                    'image_url' => env('APP_URL') . '/' . $image,
                    'caption' => $text,
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $this->accessToken]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($ch);
                curl_close($ch);
                $data = json_decode($response, true);

                if (isset($data['error'])) $errors[] = $data['error']['message'];
                if (isset($data['id'])) $creationIds[] = $data['id'];
            }

            // return [$errors, $creationIds];

            if (!empty($creationIds)) return $this->postImage2($creationIds, $text);
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function postImage2($creationIds, $text)
    {
        try {
            $url = "$this->baseUrl/$this->version/$this->igUserId/media";

            $params = [
                'children' => implode(',', $creationIds),
                'media_type' => 'CAROUSEL',
                'caption' => $text,
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $this->accessToken]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);

            $data = json_decode($response, true);
            if (isset($data['error'])) throw new Exception($data['error']['message']);

            $this->postImage3($data['id']);
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function postImage3($creationId)
    {
        try {
            $url = "$this->baseUrl/$this->version/$this->igUserId/media_publish";

            $params = [
                'creation_id' => $creationId,
                'access_token' => $this->accessToken,
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $this->accessToken]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);

            $data = json_decode($response, true);
            if (isset($data['error'])) throw new Exception($data['error']['message']);

            return [
                'status' => 200,
                'data' => $data,
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
            $this->init($ig_user_id, $user_id);

            $url = "$this->baseUrl/$this->version/$ig_user_id/media";

            $params = [
                'media_type' => 'REELS',
                'video_url' => $media,
                'upload_type' => 'resumable',
                'caption' => $title,
                'access_token' => $this->accessToken,
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: OAuth ' . $this->accessToken]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($response, true);


            if (isset($data['error'])) throw new Exception($data['error']['message']);

            $uri = $data['uri'];
            $containerId = $data['id'];

            return $this->postVideo2($uri, $containerId, $media, $mediaSize);
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function postVideo2($url, $containerId, $media, $mediaSize)
    {
        try {
            $headers = [
                'Authorization: OAuth ' . $this->accessToken,
                'offset: 0',
                'file_size: ' . $mediaSize,
                'Content-Type: application/octet-stream',
                'Content-Length: ' . $mediaSize,
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

            $data = json_decode($response, true);

            if (isset($data['error'])) throw new Exception($data['error']['message']);

            $this->postVideo3($containerId);
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function postVideo3($containerId)
    {
        try {
            $url = "$this->baseUrl/$this->version/$this->igUserId/media_publish";
            $params = [
                'creation_id' => $containerId,
                'access_token' => $this->accessToken,
            ];
            $headers = ['Authorization: OAuth ' . $this->accessToken];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $data = json_decode($response, true);

            if (isset($data['error'])) throw new Exception($data['error']['message']);

            return [
                'status' => 200,
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }
}
