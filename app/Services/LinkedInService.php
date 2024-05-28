<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;

class LinkedInService
{
    protected $baseUrl;
    protected $clientId;
    protected $clientSecret;
    protected $accessToken;
    protected $personUrn;

    public function __construct()
    {
        $this->baseUrl = 'https://api.linkedin.com/v2/';
        $this->clientId = env('LINKEDIN_CLIENT_ID');
        $this->clientSecret = env('LINKEDIN_CLIENT_SECRET');
    }

    /**
     * Auth
     */
    public function generateAuthUrl()
    {
        $param = [
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'scope' => 'openid profile email w_member_social ',
            'redirect_uri' => route('user.connect.linkedin.callback'),
            'state' => uniqid(),
        ];
        $url = 'https://www.linkedin.com/oauth/v2/authorization?' . http_build_query($param);
        return $url;
    }

    public function generateAccessToken($code)
    {
        try {
            $url = 'https://www.linkedin.com/oauth/v2/accessToken';
            $params = [
                'grant_type' => 'authorization_code',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'redirect_uri' => route('user.connect.linkedin.callback'),
                'code' => $code,
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            if ($response === false) throw new Exception(curl_error($ch));
            curl_close($ch);
            $data = json_decode($response, true);

            Auth::guard('web')->user()->linkedin_access_token = $data['access_token'];
            return $data['access_token'];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * User
     */
    public function getProfile()
    {
        try {
            $url = $this->baseUrl . 'userinfo';
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . Auth::guard('web')->user()->linkedin_access_token]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            if ($response === false) throw new Exception(curl_error($ch));
            curl_close($ch);
            $data = json_decode($response, true);
            return $data;
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Post
     * Text
     * Image
     * Video
     */

    /**
     * Text
     */
    public function postText($text, $user_id = null)
    {
        try {
            $url = $this->baseUrl . 'ugcPosts';

            if ($user_id === null) {
                $personUrn = Auth::guard('web')->user()->linkedin_urn;
                $accessToken = Auth::guard('web')->user()->linkedin_access_token;
            } else {
                $user = User::find($user_id);
                $personUrn = $user->linkedin_urn;
                $accessToken = $user->linkedin_access_token;
            }

            $params = [
                'author' => "urn:li:person:" . $personUrn,
                'lifecycleState' => 'PUBLISHED',
                'specificContent' => [
                    'com.linkedin.ugc.ShareContent' => [
                        'shareCommentary' => [
                            'text' => $text,
                        ],
                        'shareMediaCategory' => 'NONE'
                    ]
                ],
                'visibility' => [
                    'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC'
                ]
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
                'x-li-format: json'
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params)); // Encode params as JSON
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            if ($response === false) throw new Exception(curl_error($ch));
            curl_close($ch);

            $data = json_decode($response, true);

            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Image
     */
    public function postImage($imagePath, $title, $user_id = null)
    {
        try {
            $url = 'https://api.linkedin.com/v2/assets?action=registerUpload';

            if ($user_id === null) {
                $personUrn = Auth::guard('web')->user()->linkedin_urn;
                $accessToken = Auth::guard('web')->user()->linkedin_access_token;
            } else {
                $user = User::find($user_id);
                $personUrn = $user->linkedin_urn;
                $accessToken = $user->linkedin_access_token;
            }

            $params = [
                'registerUploadRequest' => [
                    'owner' => "urn:li:person:" . $personUrn,
                    'recipes' => ['urn:li:digitalmediaRecipe:feedshare-image'],
                    'serviceRelationships' => [
                        [
                            'relationshipType' => 'OWNER',
                            'identifier' => 'urn:li:userGeneratedContent'
                        ]
                    ],
                    'supportedUploadMechanism' => ['SYNCHRONOUS_UPLOAD'],
                ]
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            if ($response === false) throw new Exception(curl_error($ch));
            curl_close($ch);

            $data = json_decode($response, true);
            $upload_url = $data['value']['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']['uploadUrl'];
            $asset_id = $data['value']['asset'];

            return $this->postImage2($upload_url, $imagePath, $asset_id, $title, $accessToken, $personUrn);
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }
    public function postImage2($upload_url, $imagePath, $asset_id, $title, $accessToken, $personUrn)
    {
        try {
            $image = file_get_contents($imagePath);

            $ch = curl_init($upload_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $image);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/octet-stream',
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            if ($response === false) throw new Exception(curl_error($ch));
            curl_close($ch);

            return $this->postImage3($asset_id, $title, $accessToken, $personUrn);
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function postImage3($asset_id, $title, $accessToken, $personUrn)
    {
        try {
            $url = "https://api.linkedin.com/v2/assets/{$asset_id}/action=completeUpload";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $accessToken,
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            if ($response === false) throw new Exception(curl_error($ch));
            curl_close($ch);

            return $this->postImage4($asset_id, $title, $accessToken, $personUrn);
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }
    public function postImage4($asset_id, $title, $accessToken, $personUrn)
    {
        try {
            $url = 'https://api.linkedin.com/v2/ugcPosts';
            $params = [
                'author' => "urn:li:person:" . $personUrn,
                'lifecycleState' => 'PUBLISHED',
                'specificContent' => [
                    'com.linkedin.ugc.ShareContent' => [
                        'shareCommentary' => [
                            'text' => $title,
                        ],
                        'shareMediaCategory' => 'IMAGE',
                        'media' => [
                            [
                                'status' => 'READY',
                                'media' => $asset_id,
                                'title' => [
                                    'text' => $title,
                                ],
                            ],
                        ],
                    ],
                ],
                'visibility' => [
                    'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC',
                ],
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            if ($response === false) throw new Exception(curl_error($ch));
            curl_close($ch);

            $data = json_decode($response, true);

            if (isset($data['serviceErrorCode'])) throw new Exception($data['message']);

            return ['status' => 200];
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
    public function postVideo($video, $title, $user_id = null)
    {
        try {
            $url = 'https://api.linkedin.com/v2/assets?action=registerUpload';

            if ($user_id === null) {
                $personUrn = Auth::guard('web')->user()->linkedin_urn;
                $accessToken = Auth::guard('web')->user()->linkedin_access_token;
            } else {
                $user = User::find($user_id);
                $personUrn = $user->linkedin_urn;
                $accessToken = $user->linkedin_access_token;
            }

            $params = [
                'registerUploadRequest' => [
                    'owner' => "urn:li:person:" . $personUrn,
                    'recipes' => ['urn:li:digitalmediaRecipe:feedshare-video'],
                    'serviceRelationships' => [
                        [
                            'relationshipType' => 'OWNER',
                            'identifier' => 'urn:li:userGeneratedContent'
                        ]
                    ],
                    'supportedUploadMechanism' => ['SYNCHRONOUS_UPLOAD'],
                ]
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            if ($response === false) throw new Exception(curl_error($ch));
            curl_close($ch);

            $data = json_decode($response, true);

            $upload_url = $data['value']['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']['uploadUrl'];
            $asset_id = $data['value']['asset'];

            $postVideo2 = $this->postVideo2($upload_url, $video, $asset_id, $title, $accessToken, $personUrn);
            return $postVideo2;
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function postVideo2($upload_url, $video_path, $asset_id, $title, $accessToken, $personUrn)
    {
        try {
            $video = file_get_contents($video_path);

            $ch = curl_init($upload_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $video);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/octet-stream',
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            if ($response === false) throw new Exception(curl_error($ch));
            curl_close($ch);

            return $this->postVideo3($asset_id, $title, $accessToken, $personUrn);
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function postVideo3($asset_id, $title, $accessToken, $personUrn)
    {
        try {
            $url = "https://api.linkedin.com/v2/assets/{$asset_id}/action=completeUpload";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $accessToken,
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);

            $error = null;
            if ($response === false) $error = curl_error($ch);

            curl_close($ch);

            if ($error != null) throw new Exception($error);

            $postVideo4 = $this->postVideo4($asset_id, $title, $accessToken, $personUrn);
            return $postVideo4;
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function postVideo4($asset_id, $title, $accessToken, $personUrn)
    {
        try {
            $url = 'https://api.linkedin.com/v2/ugcPosts';
            $params = [
                'author' => "urn:li:person:" . $personUrn,
                'lifecycleState' => 'PUBLISHED',
                'specificContent' => [
                    'com.linkedin.ugc.ShareContent' => [
                        'shareCommentary' => [
                            'text' => $title,
                        ],
                        'shareMediaCategory' => 'VIDEO',
                        'media' => [
                            [
                                'status' => 'READY',
                                'media' => $asset_id,
                                'title' => [
                                    'text' => $title,
                                ],
                            ],
                        ],
                    ],
                ],
                'visibility' => [
                    'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC',
                ],
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            if ($response === false) throw new Exception(curl_error($ch));
            curl_close($ch);

            $data = json_decode($response, true);

            if (isset($data['serviceErrorCode'])) throw new Exception($data['message']);

            return ['status' => 200];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }
}
