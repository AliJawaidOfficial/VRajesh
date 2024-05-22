<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\FacebookService;
use App\Services\LinkedInService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    protected $linkedinService;
    protected $facebookService;

    public function __construct(private readonly LinkedInService $importLinkedinService, private readonly FacebookService $importFacebookService)
    {
        $this->linkedinService = $importLinkedinService;
        $this->facebookService = $importFacebookService;
    }

    public function create()
    {
        return view('user.post.create');
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'media' => 'nullable',
                    'on_facebook' => 'nullable|boolean',
                    'on_linkedin' => 'nullable|boolean',
                ],
                [
                    'title.required' => 'Title is required',
                ]
            );

            if ($validator->fails()) throw new Exception($validator->errors()->first());

            $mediaPath = null;
            $mediaType = null;

            if ($request->hasFile('media')) {
                $media = $request->file('media');
                $mediaName = time() . '.' . $media->getClientOriginalExtension();
                $mediaType = $media->getMimeType();
                $mediaSize = $media->getSize();
                $media->move(public_path('posts'), $mediaName);
                $mediaPath = public_path('posts') . '/' . $mediaName;
            }

            if ($request->has('on_linkedin')) {
                if ($request->hasFile('media')) {
                    if (str_starts_with($mediaType, 'image/')) {
                        $post = $this->linkedinService->postImage($mediaPath, $request->title);
                    } elseif (str_starts_with($mediaType, 'video/')) {
                        $post = $this->linkedinService->postVideo($mediaPath, $request->title);
                    } else {
                        throw new Exception('Invalid file type.');
                    }
                } else {
                    $post = $this->linkedinService->postText($request->title);
                }
            }

            if ($request->has('on_facebook')) {
                if ($request->hasFile('media')) {
                    if (str_starts_with($mediaType, 'image/')) {
                        $post = $this->facebookService->postImage($mediaPath, $request->title);
                    } elseif (str_starts_with($mediaType, 'video/')) {
                        $post = $this->facebookService->postVideo($mediaSize, $mediaPath, $request->title);
                    } else {
                        throw new Exception('Invalid file type.');
                    }
                } else {
                    $post = $this->facebookService->postText($request->title);
                }
            }

            Session::flash('success', ['text' => 'Posted successfully.']);
            return redirect()->back();
        } catch (Exception $e) {
            Session::flash('error', ['text' => $e->getMessage()]);
            return $e->getMessage();
        }
    }
}
