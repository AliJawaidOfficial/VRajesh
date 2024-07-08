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
    protected $refreshAccessToken;
    protected $userId;

    public function __construct()
    {
        $this->clientId = env('GOOGLE_CLIENT_ID');
        $this->clientSecret = env('GOOGLE_CLIENT_SECRET');
    }

    public function init($user_id = null)
    {
        if ($user_id === null) {
            $this->accessToken = Auth::guard('web')->user()->google_access_token;
            $this->userId = Auth::guard('web')->user()->id;
        } else {
            $user = User::where('id', $user_id)->first();
            $this->accessToken = $user->google_access_token;
            $this->userId = $user->id;
        }
    }


    // Refresh Token
    public function refreshToken($user_id = null)
    {
        $this->init($user_id);

        $user = User::find($this->userId);

        $url = 'https://oauth2.googleapis.com/token';
        $params = [
            'form_params' => [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'refresh_token' => $user->google_refresh_token,
                'grant_type' => 'refresh_token'
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        if ($response === false) throw new Exception(curl_error($ch));
        curl_close($ch);
        $data = json_decode($response, true);

        $user->google_access_token = $data['access_token'];
        $user->google_token_expires_at = now()->addSeconds($data['expires_in']);
        $user->save();
    }


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
                    'account_id' => $account_id,
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



    /**
     * =================================================================================================
     * Posts
     * =================================================================================================
     */


    /**
     * Text
     */
    public function postText($account_id, $location_id, $summary, $actionType, $callToActionURL, $user_id = null)
    {
        try {
            $this->init($user_id);

            $url = "https://mybusiness.googleapis.com/v4/$account_id/$location_id/localPosts";

            $headers = [
                'Authorization: Bearer ' . $this->accessToken,
                'Content-Type: application/json'
            ];

            $params = [
                'languageCode' => 'en-US',
                'summary' => $summary,
                'topicType' => 'STANDARD'
            ];

            if ($actionType != null) {
                if ($actionType == "CALL") {
                    $params['callToAction'] = [
                        'actionType' => $actionType,
                    ];
                } else {
                    $params['callToAction'] = [
                        'actionType' => $actionType,
                        'url' => $callToActionURL,
                    ];
                }
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $response = curl_exec($ch);
            $error = curl_error($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return $response;
            $responseData = json_decode($response, true);

            if ($httpCode != 200) throw new Exception($responseData['error']['message'] ?? 'Unknown error', $httpCode);

            return $responseData;
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'response' => $response ?? null
            ];
        }
    }



    /**
     * Image
     */
    public function postImage($account_id, $location_id, $imageUrl, $summary, $actionType, $callToActionURL, $user_id = null)
    {
        try {
            $this->init($user_id);

            $url = "https://mybusiness.googleapis.com/v4/$account_id/$location_id/localPosts";

            $headers = [
                'Authorization: Bearer ' . $this->accessToken,
                'Content-Type: application/json'
            ];

            $params = [
                'languageCode' => 'en-US',
                'summary' => $summary,
                'media' => [
                    [
                        'mediaFormat' => 'PHOTO',
                        'sourceUrl' => $imageUrl,
                    ],
                ],
                'topicType' => 'STANDARD'
            ];

            if ($actionType != null) {
                if ($actionType == "CALL") {
                    $params['callToAction'] = [
                        'actionType' => $actionType,
                    ];
                } else {
                    $params['callToAction'] = [
                        'actionType' => $actionType,
                        'url' => $callToActionURL,
                    ];
                }
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $responseData = json_decode($response, true);

            if ($httpCode != 200) throw new Exception($responseData['error']['message'] ?? 'Unknown error', $httpCode);

            return $responseData;
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'response' => $response ?? null
            ];
        }
    }



    /**
     * Video
     */
    public function postVideo($account_id, $location_id, $videoUrl, $summary, $actionType, $callToActionURL, $user_id = null)
    {
        try {
            $this->init($user_id);

            $url = "https://mybusiness.googleapis.com/v4/$account_id/$location_id/localPosts";

            $headers = [
                'Authorization: Bearer ' . $this->accessToken,
                'Content-Type: application/json'
            ];

            $params = [
                'languageCode' => 'en-US',
                'summary' => $summary,
                'callToAction' => [
                    'actionType' => $actionType,
                    'url' => $callToActionURL,
                ],
                'media' => [
                    [
                        'mediaFormat' => 'VIDEO',
                        'sourceUrl' => $videoUrl,
                    ]
                ],
                'topicType' => 'STANDARD'
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $responseData = json_decode($response, true);

            // if ($httpCode != 200) throw new Exception($responseData['error']['message'] ?? 'Unknown error', $httpCode);

            return $responseData;
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'response' => $response ?? null
            ];
        }
    }
}
