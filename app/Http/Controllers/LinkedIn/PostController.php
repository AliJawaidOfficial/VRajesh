<?php

namespace App\Http\Controllers\LinkedIn;

use App\Http\Controllers\Controller;
use CURLFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    protected $client_id;
    protected $client_secret_id;
    protected $access_token;
    protected $user;

    public function __construct()
    {
        $this->client_id = env('LINKEDIN_CLIENT_ID');
        $this->client_secret_id = env('LINKEDIN_CLIENT_SECRET');
    }

    public function textCreate()
    {
        return view('user.linkedin.post.text.create');
    }

    public function textStore(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'text' => 'required',
                ],
                [
                    'text.required' => 'Text is required',
                ]
            );

            if ($validator->fails()) throw new Exception($validator->errors()->first());

            // ===================================================

            $url = 'https://api.linkedin.com/v2/ugcPosts';
            $params = [
                'author' => "urn:li:person:" . Session::get('linkedin_user')['sub'],
                'lifecycleState' => 'PUBLISHED',
                'specificContent' => [
                    'com.linkedin.ugc.ShareContent' => [
                        'shareCommentary' => [
                            'text' => $request->text,
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
                'Authorization: Bearer ' . Session::get('linkedin_access_token'),
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

            Session::flash('success', ['text' => 'Post created successfully']);
            return redirect()->back();
        } catch (Exception $e) {
            Session::flash('error', ['text' => $e->getMessage()]);
            return redirect()->back();
        }
    }

    public function videoCreate()
    {
        return view('user.linkedin.post.video.create');
    }

    public function videoStore(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'video' => 'required|file',
                    'text' => 'nullable',
                    'title' => 'nullable',
                    'description' => 'nullable',
                ],
                [
                    'video.required' => 'Video is required',
                ]
            );

            if ($validator->fails()) throw new Exception($validator->errors()->first());

            $text = $request->text;
            $title = $request->title;
            $description = $request->description;

            $videoFile = $request->file('video');
            $videoName = time() . '.' . $videoFile->getClientOriginalExtension();
            $videoFile->move(public_path('temp'), $videoName);
            $videoPath = public_path('temp') . '/' . $videoName;

            // =====================================================

            // Step 1
            $response = $this->videoStep1($videoPath, $text, $title, $description);
            if ($response['status'] == 200) unlink($videoPath);

            Session::flash('success', ['text' => 'Post created successfully']);
            return redirect()->back();
        } catch (Exception $e) {
            Session::flash('error', ['text' => $e->getMessage()]);
            return redirect()->back();
        }
    }

    public function videoStep1($video, $text = null, $title = null, $description = null)
    {
        try {
            $url = 'https://api.linkedin.com/v2/assets?action=registerUpload';
            $params = [
                'registerUploadRequest' => [
                    'owner' => "urn:li:person:" . Session::get('linkedin_user')['sub'],
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
                'Authorization: Bearer ' . Session::get('linkedin_access_token'),
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params)); // Encode params as JSON
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            if ($response === false) throw new Exception(curl_error($ch));
            curl_close($ch);

            $data = json_decode($response, true);

            $upload_url = $data['value']['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']['uploadUrl'];
            $asset_id = $data['value']['asset'];

            $videoStep2 = $this->videoStep2($upload_url, $video, $asset_id, $text, $title, $description);
            return $videoStep2;
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function videoStep2($upload_url, $video_path, $asset_id, $text = null, $title = null, $description = null)
    {
        try {
            $video = file_get_contents($video_path);

            $ch = curl_init($upload_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $video);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . Session::get('linkedin_access_token'),
                'Content-Type: application/octet-stream',
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            if ($response === false) throw new Exception(curl_error($ch));
            curl_close($ch);

            return $this->videoStep3($asset_id, $text, $title, $description);
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function videoStep3($asset_id, $text = null, $title = null, $description = null)
    {
        try {
            $url = "https://api.linkedin.com/v2/assets/{$asset_id}/action=completeUpload";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . Session::get('linkedin_access_token'),
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);

            $error = null;
            if ($response === false) $error = curl_error($ch);

            curl_close($ch);

            if ($error != null) throw new Exception($error);

            $videoStep4 = $this->videoStep4($asset_id, $text, $title, $description);
            return $videoStep4;
        } catch (Exception $e) {
            return [
                'status' => 500,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function videoStep4($asset_id, $text, $title, $description)
    {
        try {
            $url = 'https://api.linkedin.com/v2/ugcPosts';
            $params = [
                'author' => "urn:li:person:" . Session::get('linkedin_user')['sub'],
                'lifecycleState' => 'PUBLISHED',
                'specificContent' => [
                    'com.linkedin.ugc.ShareContent' => [
                        'shareCommentary' => [
                            'text' => $text,
                        ],
                        'shareMediaCategory' => 'VIDEO',
                        'media' => [
                            [
                                'status' => 'READY',
                                'description' => [
                                    'text' => $description,
                                ],
                                'media' => $asset_id,
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
                'Authorization: Bearer ' . Session::get('linkedin_access_token'),
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
