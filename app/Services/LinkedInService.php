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

    protected $clientId2;
    protected $clientSecret2;

    protected $accessToken;
    protected $community_accessToken;

    protected $personUrn;
    protected $organizationUrn;

    public function __construct()
    {
        $this->baseUrl = 'https://api.linkedin.com/v2/';
        $this->clientId = env('LINKEDIN_CLIENT_ID');
        $this->clientSecret = env('LINKEDIN_CLIENT_SECRET');
        $this->clientId2 = env('LINKEDIN_CLIENT_ID_2');
        $this->clientSecret2 = env('LINKEDIN_CLIENT_SECRET_2');
    }

    /**
     * Auth
     */
    public function generateAuthUrl()
    {
        $param = [
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'scope' => 'openid profile email w_member_social r_organization_admin',
            'redirect_uri' => route('user.connect.linkedin.callback'),
            'state' => uniqid(),
        ];
        $url = 'https://www.linkedin.com/oauth/v2/authorization?' . http_build_query($param);
        return $url;
    }

    public function generateAuthUrl2()
    {
        $param = [
            'response_type' => 'code',
            'client_id' => $this->clientId2,
            'scope' => 'w_organization_social',
            'redirect_uri' => route('user.connect.linkedin.callback.2'),
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

            return $data['access_token'];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function generateAccessToken2($code)
    {
        try {
            $url = 'https://www.linkedin.com/oauth/v2/accessToken';
            $params = [
                'grant_type' => 'authorization_code',
                'client_id' => $this->clientId2,
                'client_secret' => $this->clientSecret2,
                'redirect_uri' => route('user.connect.linkedin.callback.2'),
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

            return $data['access_token'];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function init($user_id = null)
    {
        if ($user_id === null) {
            $this->personUrn = Auth::guard('web')->user()->linkedin_urn;
            $this->accessToken = Auth::guard('web')->user()->linkedin_access_token;
        } else {
            $user = User::find($user_id);
            $this->personUrn = $user->linkedin_urn;
            $this->accessToken = $user->linkedin_access_token;
        }
    }

    public function setOrganizationUrn($organization_id)
    {
        $this->organizationUrn = $organization_id;
    }

    public function setCommunityAccessToken()
    {
        $this->community_accessToken = Auth::guard('web')->user()->linkedin_community_access_token;
    }

    /**
     * User
     */
    public function getProfile($access_token)
    {
        try {
            $url = $this->baseUrl . 'userinfo';
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $access_token]);
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
     * Get Organizations
     */
    public function getOrganizations($user_id = null)
    {
        $this->init($user_id);

        try {
            $url = 'https://api.linkedin.com/rest/organizationAcls?q=roleAssignee';

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

            if ($httpCode != 200) throw new Exception('LinkedIn API error: ' . $response);

            $organizations = [];
            foreach ($data['elements'] as $element) {
                if (isset($element['organization'])) {
                    $organization_id = substr($element['organization'], strrpos($element['organization'], ":") + 1);
                    $orgDetails = $this->getOrganizationDetails($organization_id);

                    if ($orgDetails) $organizations[] = $orgDetails;
                }
            }

            return $organizations;
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage(),
            ];
        }
    }


    public function getOrganizationDetails($organizationId)
    {
        try {
            $url = "https://api.linkedin.com/rest/organizations/$organizationId";

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

            if ($response === false) throw new Exception(curl_error($ch));

            $data = json_decode($response, true);

            if ($httpCode != 200) throw new Exception('LinkedIn API error: ' . $response);

            return [
                'id' => $data['id'],
                'name' => $data['localizedName'],
                'vanity_name' => $data['vanityName'],
            ];
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
    public function postText($organization_id, $text, $user_id = null)
    {
        try {
            $url = 'https://api.linkedin.com/rest/posts';

            $this->init($user_id);

            $this->setOrganizationUrn($organization_id);
            $this->setCommunityAccessToken();

            $params = [
                'author' => "urn:li:organization:" . $this->organizationUrn,
                "commentary" => $text,
                "visibility" => "PUBLIC",
                "distribution" => [
                    "feedDistribution" => "MAIN_FEED",
                    "targetEntities" => [],
                    "thirdPartyDistributionChannels" => [],
                ],
                "lifecycleState" => "PUBLISHED",
                "isReshareDisabledByAuthor" => false
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->community_accessToken,
                'X-Restli-Protocol-Version: 2.0.0',
                'LinkedIn-Version: 202403',
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($response === false) throw new Exception(curl_error($ch));
            curl_close($ch);

            if (isset($data['serviceErrorCode'])) throw new Exception($data['message']);

            $data = json_decode($response, true);

            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Image
     */
    public function postImage($organization_id, $imagePath, $title, $user_id = null)
    {
        try {
            $url = 'https://api.linkedin.com/v2/assets?action=registerUpload';

            $this->init($user_id);
            $this->setOrganizationUrn($organization_id);
            $this->setCommunityAccessToken();

            $params = [
                'registerUploadRequest' => [
                    'owner' => "urn:li:organization:" . $this->organizationUrn,
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
                'Authorization: Bearer ' . $this->community_accessToken,
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            if ($response === false) throw new Exception(curl_error($ch));
            curl_close($ch);

            if (isset($data['serviceErrorCode'])) throw new Exception($data['message']);

            $data = json_decode($response, true);
            $upload_url = $data['value']['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']['uploadUrl'];
            $asset_id = $data['value']['asset'];

            return $this->postImage2($upload_url, $imagePath, $asset_id, $title);
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function postImage2($upload_url, $imagePath, $asset_id, $title)
    {
        try {
            $image = file_get_contents($imagePath);

            $ch = curl_init($upload_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $image);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->community_accessToken,
                'Content-Type: application/octet-stream',
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            if ($response === false) throw new Exception(curl_error($ch));
            curl_close($ch);

            return $this->postImage3($asset_id, $title);
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function postImage3($asset_id, $title)
    {
        try {
            $url = "https://api.linkedin.com/v2/assets/{$asset_id}/action=completeUpload";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->community_accessToken,
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            if ($response === false) throw new Exception(curl_error($ch));
            curl_close($ch);

            return $this->postImage4($asset_id, $title);
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function postImage4($asset_id, $title)
    {
        try {
            $url = 'https://api.linkedin.com/v2/ugcPosts';
            $params = [
                'author' => "urn:li:organization:" . $this->organizationUrn,
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
                'Authorization: Bearer ' . $this->community_accessToken,
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
    public function postVideo($organization_id, $video, $title, $user_id = null)
    {
        try {
            $url = 'https://api.linkedin.com/v2/assets?action=registerUpload';

            $this->init($user_id);
            $this->setOrganizationUrn($organization_id);
            $this->setCommunityAccessToken();

            $params = [
                'registerUploadRequest' => [
                    'owner' => "urn:li:person:" . $this->organizationUrn,
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
                'Authorization: Bearer ' . $this->community_accessToken,
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            if ($response === false) throw new Exception(curl_error($ch));
            curl_close($ch);

            $data = json_decode($response, true);

            if (isset($data['serviceErrorCode'])) throw new Exception($data['message']);
            
            $upload_url = $data['value']['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']['uploadUrl'];
            $asset_id = $data['value']['asset'];

            $postVideo2 = $this->postVideo2($upload_url, $video, $asset_id, $title);
            return $postVideo2;
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function postVideo2($upload_url, $video_path, $asset_id, $title)
    {
        try {
            $video = file_get_contents($video_path);

            $ch = curl_init($upload_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $video);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->community_accessToken,
                'Content-Type: application/octet-stream',
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            if ($response === false) throw new Exception(curl_error($ch));
            curl_close($ch);

            return $this->postVideo3($asset_id, $title);
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function postVideo3($asset_id, $title)
    {
        try {
            $url = "https://api.linkedin.com/v2/assets/{$asset_id}/action=completeUpload";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->community_accessToken,
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);

            $error = null;
            if ($response === false) $error = curl_error($ch);

            curl_close($ch);

            if ($error != null) throw new Exception($error);

            $postVideo4 = $this->postVideo4($asset_id, $title);
            return $postVideo4;
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function postVideo4($asset_id, $title)
    {
        try {
            $url = 'https://api.linkedin.com/v2/ugcPosts';
            $params = [
                'author' => "urn:li:organization:" . $this->organizationUrn,
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
                'Authorization: Bearer ' . $this->community_accessToken,
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            if ($response === false) throw new Exception(curl_error($ch));
            curl_close($ch);

            $data = json_decode($response, true);

            if (isset($data['serviceErrorCode'])) throw new Exception($data['message']);

            return $data;
            return ['status' => 200];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }


    /**
     * Indivisual Post
     * Text
     * Image
     * Video
     */

    /**
     * Text
     */
    public function individualPostText($text, $user_id = null)
    {
        try {
            $url = $this->baseUrl . 'ugcPosts';

            $this->init($user_id);

            $params = [
                'author' => "urn:li:person:" . $this->personUrn,
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
                'Authorization: Bearer ' . $this->accessToken,
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

    public function individualPostImage($imagePath, $title, $user_id = null)
    {
        try {
            $url = 'https://api.linkedin.com/v2/assets?action=registerUpload';

            $this->init($user_id);

            $params = [
                'registerUploadRequest' => [
                    'owner' => "urn:li:person:" . $this->personUrn,
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
                'Authorization: Bearer ' . $this->accessToken,
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

            return $this->individualPostImage2($upload_url, $imagePath, $asset_id, $title);
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function individualPostImage2($upload_url, $imagePath, $asset_id, $title)
    {
        try {
            $image = file_get_contents($imagePath);

            $ch = curl_init($upload_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $image);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->accessToken,
                'Content-Type: application/octet-stream',
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            if ($response === false) throw new Exception(curl_error($ch));
            curl_close($ch);

            return $this->individualPostImage3($asset_id, $title);
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function individualPostImage3($asset_id, $title)
    {
        try {
            $url = "https://api.linkedin.com/v2/assets/{$asset_id}/action=completeUpload";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->accessToken,
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            if ($response === false) throw new Exception(curl_error($ch));
            curl_close($ch);

            return $this->individualPostImage4($asset_id, $title);
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function individualPostImage4($asset_id, $title)
    {
        try {
            $url = 'https://api.linkedin.com/v2/ugcPosts';
            $params = [
                'author' => "urn:li:person:" . $this->personUrn,
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
                'Authorization: Bearer ' . $this->accessToken,
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

    public function individualPostVideo($video, $title, $user_id = null)
    {
        try {
            $url = 'https://api.linkedin.com/v2/assets?action=registerUpload';

            $this->init($user_id);

            $params = [
                'registerUploadRequest' => [
                    'owner' => "urn:li:person:" . $this->personUrn,
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
                'Authorization: Bearer ' . $this->accessToken,
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

            $postVideo2 = $this->individualPostVideo2($upload_url, $video, $asset_id, $title);
            return $postVideo2;
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function individualPostVideo2($upload_url, $video_path, $asset_id, $title)
    {
        try {
            $video = file_get_contents($video_path);

            $ch = curl_init($upload_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $video);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->accessToken,
                'Content-Type: application/octet-stream',
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            if ($response === false) throw new Exception(curl_error($ch));
            curl_close($ch);

            return $this->individualPostVideo3($asset_id, $title);
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function individualPostVideo3($asset_id, $title)
    {
        try {
            $url = "https://api.linkedin.com/v2/assets/{$asset_id}/action=completeUpload";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->accessToken,
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);

            $error = null;
            if ($response === false) $error = curl_error($ch);

            curl_close($ch);

            if ($error != null) throw new Exception($error);

            $postVideo4 = $this->individualPostVideo4($asset_id, $title);
            return $postVideo4;
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function individualPostVideo4($asset_id, $title)
    {
        try {
            $url = 'https://api.linkedin.com/v2/ugcPosts';
            $params = [
                'author' => "urn:li:person:" . $this->personUrn,
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
                'Authorization: Bearer ' . $this->accessToken,
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
