<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class GoogleService
{
    protected $clientId;
    protected $clientSecret;
    protected $accessToken;

    public function __construct()
    {
        $this->clientId = env('GOOGLE_CLIENT_ID');
        $this->clientSecret = env('GOOGLE_CLIENT_SECRET');
    }

    public function init($user_id = null)
    {
        if ($user_id === null) {
            $this->accessToken = Auth::guard('web')->user()->google_access_token;
        } else {
            $user = User::where('id', $user_id)->first();
            $this->accessToken = $user->google_access_token;
        }
    }


    // Refresh Token
    // public function refreshToken($user_id = null)
    // {
    //     $this->init($user_id);

    //     $url = 'https://oauth2.googleapis.com/token';
    //     $params = [
    //         'form_params' => [
    //             'client_id' => $this->clientId,
    //             'client_secret' => $this->clientSecret,
    //             'refresh_token' => $this->accessToken,
    //             'grant_type' => 'refresh_token'
    //         ]
    //     ];

    //     $ch = curl_init($url);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    //     $response = curl_exec($ch);
    //     if ($response === false) throw new Exception(curl_error($ch));
    //     curl_close($ch);
    //     $data = json_decode($response, true);
    //     return $data;
    //     // return $data['access_token'];
    // }


    /**
     * =================================================================================================
     * Accounts
     * =================================================================================================
     */
    public function getBusinessProfiles($user_id = null)
    {
        $this->init($user_id);

        $data = [];

        $accounts = $this->getAccounts();

        foreach ($accounts['accounts'] as $account) {
            $account_id = $account['name'];
            $locations = $this->getLocations($account_id);
            foreach ($locations as $location) {
                $data[] = [
                    'location_id' => $location[0]['name'],
                    'location_name' => $location[0]['title'],
                    'location_phone' => $location[0]['phoneNumbers']['primaryPhone'],
                ];
            }
        }

        return $data;
    }

    public function getAccounts()
    {
        try {
            $url = 'https://mybusinessaccountmanagement.googleapis.com/v1/accounts';

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->accessToken,
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $data = json_decode($response, true);

            if ($httpCode != 200) throw new Exception($data['error']['message']);

            return $data;
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    public function getLocations($account_id)
    {
        try {
            $fields = [
                'title',
                'name',
                'phoneNumbers',
                // . 'labels',
                // 'address',
                // 'primaryCategory',
                // 'additionalCategories',
                // 'websiteUrl',
                // . 'latlng',
                // . 'metadata',
                // . 'adWordsLocationExtensions',
                // . 'labels',
                // . 'openInfo',
                // . 'profile',
                // . 'regularHours',
                // . 'moreHours',
                // . 'serviceItems',
            ];
            $params = implode(',', $fields);
            $url = "https://mybusinessbusinessinformation.googleapis.com/v1/$account_id/locations?readMask=$params";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->accessToken,
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $data = json_decode($response, true);

            if ($httpCode != 200) throw new Exception(isset($data['error']['message']) ? $data['error']['message'] : 'An error occurred while fetching business locations.');

            return $data;
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage()
            ];
        }
    }

    // public function getLocation($location_id)
    // {
    //     try {
    //         $url = "https://mybusinessbusinessinformation.googleapis.com/v1/$location_id?readMask=name,phoneNumbers";
    //         $ch = curl_init($url);
    //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //         curl_setopt($ch, CURLOPT_HTTPHEADER, [
    //             'Authorization: Bearer ' . $this->accessToken,
    //             'Content-Type: application/json'
    //         ]);
    //         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    //         $response = curl_exec($ch);
    //         $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //         curl_close($ch);
    //         $data = json_decode($response, true);

    //         return $data;

    //         if ($httpCode != 200) throw new Exception(isset($data['error']['message']) ? $data['error']['message'] : 'An error occurred while fetching business locations.');

    //         return $data;
    //     } catch (Exception $e) {
    //         return [
    //             'error' => $e->getMessage()
    //         ];
    //     }
    // }



    /**
     * =================================================================================================
     * Posts
     * =================================================================================================
     */


    /**
     * Text
     */
    public function postText($message, $user_id = null)
    {
        try {
            $this->init($user_id);

            $url = 'https://mybusiness.googleapis.com/v4/accounts/{YOUR_ACCOUNT_ID}/locations/{YOUR_LOCATION_ID}/localPosts';

            $headers = [
                'Authorization: Bearer ' . $this->accessToken,
                'Content-Type: application/json'
            ];

            $params = [
                'languageCode' => 'en',
                'summary' => $message,
                'callToAction' => [
                    'actionType' => 'LEARN_MORE',
                    'url' => 'https://example.com'
                ]
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $responseData = json_decode($response, true);

            if ($httpCode != 200) throw new Exception($responseData['error']['message']);

            return $responseData;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }


    /**
     * Image
     */
    public function postImage($message, $imagePath, $user_id = null)
    {
        try {
            $this->init($user_id);

            $url = 'https://mybusiness.googleapis.com/v4/accounts/{YOUR_ACCOUNT_ID}/locations/{YOUR_LOCATION_ID}/localPosts';

            $headers = [
                'Authorization: Bearer ' . $this->accessToken,
                'Content-Type: multipart/related; boundary=foo_bar_baz'
            ];

            // Create multipart data
            $boundary = uniqid();
            $postData = "--foo_bar_baz\r\n";
            $postData .= "Content-Type: application/json; charset=UTF-8\r\n\r\n";
            $postData .= json_encode([
                'languageCode' => 'en',
                'summary' => $message,
                'callToAction' => [
                    'actionType' => 'LEARN_MORE',
                    'url' => 'https://example.com'
                ]
            ]);
            $postData .= "\r\n--foo_bar_baz\r\n";
            $postData .= "Content-Type: image/jpeg\r\n";
            $postData .= "Content-Transfer-Encoding: base64\r\n";
            $postData .= "\r\n";
            $postData .= base64_encode(file_get_contents($imagePath)) . "\r\n";
            $postData .= "--foo_bar_baz--";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $responseData = json_decode($response, true);

            if ($httpCode != 200) throw new Exception($responseData['error']['message']);

            return $responseData;
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }
}
