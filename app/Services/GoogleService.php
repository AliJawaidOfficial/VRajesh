<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;

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



    /**
     * =================================================================================================
     * Accounts
     * =================================================================================================
     */
    public function getAccounts($user_id = null)
    {
        $this->init($user_id);

        try {
            $url = 'https://mybusinessaccountmanagement.googleapis.com/v1/accounts';

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->accessToken,
                'X-Restli-Protocol-Version: 2.0.0',
                'LinkedIn-Version: 202403'
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $data = json_decode($response, true);

            if ($httpCode != 200) throw new Exception($data['error']['message']);

            return $data;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }



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
