<?php

namespace App\Http\Controllers\Facebook;

use App\Http\Controllers\Controller;
use CURLFile;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    protected $version;
    protected $app_id;
    protected $app_secret;
    protected $page_id;
    protected $page_access_token;

    public function __construct()
    {
        $this->version = env('FACEBOOK_GRAPH_VERSION');
        $this->app_id = env('FACEBOOK_CLIENT_ID');
        $this->page_id = env('FACEBOOK_PAGE_ID');
        $this->page_access_token = env('FACEBOOK_PAGE_ACCESS_TOKEN');
    }

    public function imageCreate()
    {
        return view('user.facebook.post.image.create');
    }

    public function imageStore(Request $request)
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

            $imageFile = $request->file('image');
            // ===================================================

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://graph-video.facebook.com/$this->version/$this->page_id/photos");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                "access_token" => $this->page_access_token,
                "message" => $request->text,
                "source" => new CURLFile($imageFile),
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
            Session::flash('error', ['text' => $e->getMessage()]);
            return redirect()->back();
        }
    }

    public function textCreate()
    {
        return view('user.facebook.post.text.create');
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

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://graph-video.facebook.com/$this->version/$this->page_id/feed");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                "message" => $request->text,
                "access_token" => env('FACEBOOK_PAGE_ACCESS_TOKEN'),
                "published" => true,
            ]));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);

            if (isset($response['error'])) throw new Exception($response['error']['message']);

            Session::flash('success', ['text' => 'Post created successfully']);
            return redirect()->back();
        } catch (Exception $e) {
            Session::flash('error', ['text' => $e->getMessage()]);
            return redirect()->back();
        }
    }

    public function videoCreate()
    {
        return view('user.facebook.post.video.create');
    }

    public function videoStore(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'video' => 'required|file',
                    'description' => 'nullable',
                ],
                [
                    'video.required' => 'Video is required',
                ]
            );

            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $videoFile = $request->file('video');
            // =====================================================

            // Step 1
            $response = $this->videoStep1($videoFile->getSize());
            if (isset($response['error'])) throw new Exception($response['error']['message']);

            $responseData = json_decode($response, true);
            $uploadSessionId = $responseData['upload_session_id'];

            // Step 2
            $step2response = $this->videoStep2($uploadSessionId, $videoFile);
            if (isset($step2response['error'])) throw new Exception($step2response['error']['message']);

            // Step 3
            $step3response = $this->videoStep3($uploadSessionId, $request->description);
            if (isset($step3response['error'])) throw new Exception($step3response['error']['message']);

            Session::flash('success', ['text' => 'Post created successfully']);
            return redirect()->back();
        } catch (Exception $e) {
            Session::flash('error', ['text' => $e->getMessage()]);
            return redirect()->back();
        }
    }

    function videoStep1($fileSize)
    {
        $url = "https://graph-video.facebook.com/$this->version/$this->page_id/videos";
        $postFields = [
            'upload_phase' => 'start',
            'access_token' => $this->page_access_token,
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

    function videoStep2($uploadSessionId, $file)
    {
        $url = "https://graph-video.facebook.com/$this->version/$this->page_id/videos";
        $postFields = [
            'upload_phase' => 'transfer',
            'access_token' => $this->page_access_token,
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

    function videoStep3($uploadSessionId, $description = null)
    {
        $url = "https://graph-video.facebook.com/$this->version/$this->page_id/videos";
        $postFields = [
            'upload_phase' => 'finish',
            'access_token' => $this->page_access_token,
            'upload_session_id' => $uploadSessionId,
            'description' => $description,
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
