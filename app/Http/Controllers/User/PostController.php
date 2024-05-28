<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Rules\AtLeastOnePlatform;
use App\Services\FacebookService;
use App\Services\LinkedInService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    protected $linkedinService;
    protected $facebookService;

    public function __construct(
        private readonly LinkedInService $importLinkedinService,
        private readonly FacebookService $importFacebookService
    ) {
        $this->linkedinService = $importLinkedinService;
        $this->facebookService = $importFacebookService;
    }

    public function index(Request $request)
    {
        $user = Auth::guard('web')->user();

        $postMonths = Post::where('user_id', $user->id)
            ->where('posted', 1)
            ->where('draft', 0)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('Y-m');
            })
            ->keys();

        $dataSet = Post::where('user_id', $user->id)
            ->where('posted', 1)
            ->where('draft', 0)
            ->orderBy('created_at', 'DESC');

        if ($request->has('month') && $request->month != '') $dataSet = $dataSet->whereYear('created_at', '=', Carbon::parse($request->month)->year)
            ->whereMonth('created_at', '=', Carbon::parse($request->month)->month);

        $dataSet = $dataSet->paginate(10);

        return view('user.post.index', compact('dataSet', 'postMonths'));
    }

    public function show(String $id)
    {
        try {
            $user = Auth::guard('web')->user();
            $data = Post::where('user_id', $user->id)->where('id', $id)->first();

            return response()->json([
                'status' => 200,
                'data' => $data
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function scheduled()
    {
        return view('user.post.scheduled');
    }

    public function scheduledData(Request $request)
    {
        try {
            $user = Auth::guard('web')->user();
            $data = Post::where('user_id', $user->id)->whereNotNull('scheduled_at')
                ->where('scheduled_at', '>=', $request->start)
                ->where('scheduled_at', '<=', $request->end)
                ->get();

            return $data;
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function scheduledDataShow(String $id)
    {
        try {
            $user = Auth::guard('web')->user();
            $data = Post::where('user_id', $user->id)->where('id', $id)->first();

            return $data;
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function create()
    {
        return view('user.post.create');
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Atleast one platform required
            if (!$request->has('on_facebook') && !$request->has('on_instagram') && !$request->has('on_linkedin')) throw new Exception('Please select at least one platform.');

            $validator = Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'description' => 'nullable|required_without:media',
                    'media' => 'nullable',
                    'on_facebook' => 'nullable|boolean',
                    'on_instagram' => 'nullable|boolean',
                    'on_linkedin' => 'nullable|boolean',
                    'schedule_date' => 'nullable|date',
                    'schedule_time' => 'nullable|date_format:H:i',
                ],
                [
                    'title.required' => 'Title is required',

                    'description.required' => 'Description is required',
                    'description.required_without' => 'Description is required',

                    'media.required' => 'Media is required',
                    'media.required_if' => 'Media is required',
                ]
            );

            if ($validator->fails()) throw new Exception($validator->errors()->first());

            $data = new Post;
            $data->user_id = Auth::guard('web')->user()->id;
            $data->title = $request->title;
            $data->description = $request->description;

            $mediaPath = null;
            $mediaType = null;
            $media_type = null;

            if ($request->hasFile('media')) {
                $media = $request->file('media');
                $mediaName = time() . '.' . $media->getClientOriginalExtension();
                $mediaType = $media->getMimeType();
                $mediaSize = $media->getSize();
                $media->move(public_path('posts'), $mediaName);
                $onlyMediaPath = 'posts/' . $mediaName;
                $mediaPath = public_path('posts') . '/' . $mediaName;

                if (str_starts_with($mediaType, 'image/')) {
                    $media_type = 'image';
                } elseif (str_starts_with($mediaType, 'video/')) {
                    $media_type = 'video';
                }

                $data->media = $onlyMediaPath;
                $data->media_type = $media_type;
            }

            // Posting disabled for now
            if ($request->schedule_date == null && $request->schedule_time == null) {
                if ($request->has('on_facebook')) {
                    $data->on_facebook = 1;
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

                if ($request->has('on_instagram')) {
                    $data->on_instagram = 1;
                }

                if ($request->has('on_linkedin')) {
                    $data->on_linkedin = 1;
                    if ($request->hasFile('media')) {
                        if ($media_type == 'image') {
                            $post = $this->linkedinService->postImage($mediaPath, $request->title);
                        } elseif ($media_type == 'video') {
                            $post = $this->linkedinService->postVideo($mediaPath, $request->title);
                        } else {
                            throw new Exception('Invalid file type.');
                        }
                    } else {
                        $post = $this->linkedinService->postText($request->title);
                    }
                }

                $data->posted = 1;
            } else {
                $data->scheduled_at = $request->schedule_date . ' ' . $request->schedule_time;

                if ($request->has('on_facebook')) $data->on_facebook = 1;
                if ($request->has('on_instagram')) $data->on_instagram = 1;
                if ($request->has('on_linkedin')) $data->on_linkedin = 1;
            }

            $data->save();

            DB::commit();

            return response()->json([
                'status' => 200,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function draft(Request $request)
    {
        $user = Auth::guard('web')->user();

        $postMonths = Post::where('user_id', $user->id)
            ->where('posted', 0)
            ->where('draft', 1)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('Y-m');
            })
            ->keys();

        $dataSet = Post::where('user_id', $user->id)
            ->where('posted', 0)
            ->where('draft', 1)
            ->orderBy('created_at', 'DESC');

        if ($request->has('month') && $request->month != '') $dataSet = $dataSet->whereYear('created_at', '=', Carbon::parse($request->month)->year)
            ->whereMonth('created_at', '=', Carbon::parse($request->month)->month);

        $dataSet = $dataSet->paginate(10);

        return view('user.post.draft', compact('dataSet', 'postMonths'));
    }

    public function draftStore(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'description' => 'required',
                    'media' => 'nullable',
                    'on_facebook' => 'nullable|boolean',
                    'on_instagram' => 'nullable|boolean',
                    'on_linkedin' => 'nullable|boolean',
                    'schedule_date' => 'nullable|date',
                    'schedule_time' => 'nullable|date_format:H:i',
                ],
                [
                    'title.required' => 'Title is required',

                    'description.required' => 'Description is required',

                    'media.required_if' => 'Media is required',
                ]
            );

            if ($validator->fails()) throw new Exception($validator->errors()->first());

            $data = new Post;
            $data->user_id = Auth::guard('web')->user()->id;
            $data->title = $request->title;
            $data->description = $request->description;
            $data->draft = 1;

            $mediaPath = null;
            $mediaType = null;
            $media_type = null;

            if ($request->hasFile('media')) {
                $media = $request->file('media');
                $mediaName = time() . '.' . $media->getClientOriginalExtension();
                $mediaType = $media->getMimeType();
                $media->move(public_path('posts'), $mediaName);
                $mediaPath = public_path('posts') . '/' . $mediaName;

                if (str_starts_with($mediaType, 'image/')) {
                    $media_type = 'image';
                } elseif (str_starts_with($mediaType, 'video/')) {
                    $media_type = 'video';
                }

                $data->media = $mediaPath;
                $data->media_type = $media_type;
            }

            if ($request->has('on_facebook')) $data->on_facebook = 1;
            if ($request->has('on_instagram')) $data->on_instagram = 1;
            if ($request->has('on_linkedin')) $data->on_linkedin = 1;
            if ($request->schedule_date != null && $request->schedule_time != null) $data->scheduled_at = $request->schedule_date . ' ' . $request->schedule_time;

            $data->save();

            DB::commit();

            return response()->json([
                'status' => 200,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage()
            ]);
        }
    }
}
