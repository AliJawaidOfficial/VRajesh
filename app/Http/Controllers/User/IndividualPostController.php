<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\LinkedInService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class IndividualPostController extends Controller
{
    protected $linkedinService;

    public function __construct(private readonly LinkedInService $importLinkedinService)
    {
        $this->linkedinService = $importLinkedinService;
    }

    /**
     * Post Posts
     */
    public function index(Request $request)
    {
        $user = Auth::guard('web')->user();

        $postMonths = Post::where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('on_linkedin', 1)->whereNull('linkedin_company_id');
                $q->orWhere('on_facebook', 1)->whereNull('facebook_page_id');
                $q->orWhere('on_instagram', 1)->whereNull('instagram_account_id');
            })
            ->where('posted', 1)
            ->where('draft', 0)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('Y-m');
            })
            ->keys();

        $dataSet = Post::where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('on_linkedin', 1)->whereNull('linkedin_company_id');
                $q->orWhere('on_facebook', 1)->whereNull('facebook_page_id');
                $q->orWhere('on_instagram', 1)->whereNull('instagram_account_id');
            })
            ->where('posted', 1)
            ->where('draft', 0)
            ->orderBy('created_at', 'DESC');

        if ($request->has('month') && $request->month != '') $dataSet = $dataSet->whereYear('created_at', '=', Carbon::parse($request->month)->year)
            ->whereMonth('created_at', '=', Carbon::parse($request->month)->month);

        $dataSet = $dataSet->select(
            'posts.*',
            DB::raw('(SELECT COUNT(*) FROM `posts` AS `post_2` WHERE `posts`.`post_id`=`post_2`.`id`) + 1 AS `post_count`'),
        );

        $dataSet = $dataSet->paginate(10);

        return view('user.individual-post.index', compact('dataSet', 'postMonths'));
    }

    /**
     * Post Details
     */
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

    /**
     * Post Create
     */
    public function create(Request $request)
    {
        $schedule_date = $request->has('schedule_date') ? $request->schedule_date : null;
        return view('user.individual-post.create', compact('schedule_date'));
    }

    /**
     * Post Store
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'description' => 'nullable|required_without:media',
                    'media' => 'nullable',
                    'schedule_date' => 'nullable|date|after_or_equal:today',
                    'schedule_time' => 'nullable|date_format:H:i',
                ],
                [
                    'title.required' => 'Title is required',

                    'description.required' => 'Description is required',
                    'description.required_without' => 'Description is required',

                    'media.required' => 'Media is required',
                    'media.required_if' => 'Media is required for Instagram.',

                    'schedule_date.after_or_equal' => 'Schedule date should be a future date.',

                    'schedule_time.after_or_equal' => 'Schedule time should be a future time.',
                ]
            );

            $validator->after(function ($validator) use ($request) {
                if ($request->has('media') && $request->media_type == 'image') {
                    foreach ($request->media as $media) {
                        if (!in_array($media->extension(), ['jpg', 'jpeg', 'png', 'gif'])) $validator->errors()->add('media', 'Media should be jpg, jpeg and png');
                    }
                }

                if ($request->has('media') && $request->media_type == 'video') {
                    foreach ($request->media as $media) {
                        if (!in_array($media->extension(), ['mp4', 'mpeg', 'avi'])) $validator->errors()->add('media', 'Media should be mp4, mpeg and avi');
                    }
                }
            });

            if ($validator->fails()) throw new Exception($validator->errors()->first());

            $data = new Post;
            $data->user_id = Auth::guard('web')->user()->id;
            $data->title = $request->title;
            $data->description = $request->description;

            if ($request->media) {
                $mediaPaths = [];
                $mediaSizes = [];

                foreach ($request->media as $media) {
                    $mediaName = time() . '_' . rand(1000, 9999) . '.' . $media->getClientOriginalExtension();
                    $mediaSize = $media->getSize();
                    $media->move(public_path('posts'), $mediaName);
                    $mediaPath = 'posts/' . $mediaName;

                    $mediaPaths[] = $mediaPath;
                    $mediaSizes[] = $mediaSize;
                }

                $data->media = implode(',', $mediaPaths);
                $data->media_type = $request->media_type;
            }

            $data->on_linkedin = 1;

            if ($request->schedule_date != null && $request->schedule_time != null) {
                $ip = $request->ip();
                $countryAndTimezone = getCountryAndTimezone($ip);
                $time = convertTimeToUtc($request->schedule_time, $countryAndTimezone['timezone']);
                $data->scheduled_at = $request->schedule_date . ' ' . $time;
            } else {
                if ($request->hasFile('media')) {
                    if ($data->media_type == 'image') $posted = $this->linkedinService->individualPostImage(
                        $data->media,
                        $request->description
                    );
                    if ($data->media_type == 'video') $posted = $this->linkedinService->individualPostVideo(
                        $data->media,
                        $request->description
                    );
                } else {
                    $posted = $this->linkedinService->individualPostText($request->description);
                }
                $data->posted = 1;
            }

            $data->save();

            DB::commit();

            if ($request->schedule_date != null && $request->schedule_time != null) return response()->json([
                'status' => 200,
                'message' => 'Post scheduled successfully'
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Post published successfully'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create new from old
     */
    public function newStore(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make(
                $request->all(),
                [
                    'post_id' => 'required|exists:posts,id',
                    'title' => 'required',
                    'description' => 'nullable|required_without:media',
                    'media' => 'nullable',
                    'on_facebook' => 'nullable|boolean',
                    'on_instagram' => 'nullable|boolean',
                    'on_linkedin' => 'nullable|boolean',
                    'schedule_date' => 'nullable|date|after_or_equal:today',
                    'schedule_time' => 'nullable|date_format:H:i',
                ],
                [
                    'post_id.required' => 'Invalid Request',
                    'post_id.exists' => 'Invalid Request',

                    'title.required' => 'Title is required',

                    'description.required' => 'Description is required',
                    'description.required_without' => 'Description is required',

                    'media.required' => 'Media is required',
                    'media.required_if' => 'Media is required',

                    'schedule_date.after_or_equal' => 'Schedule date should be a future date.',

                    'schedule_time.after_or_equal' => 'Schedule time should be a future time.',
                ]
            );

            if ($validator->fails()) throw new Exception($validator->errors()->first());

            if (!$request->has('on_facebook') && !$request->has('on_instagram') && !$request->has('on_linkedin')) throw new Exception('Please select at least one platform.');

            $data = new Post;
            $data->user_id = Auth::guard('web')->user()->id;
            $data->post_id = $request->post_id;
            $data->title = $request->title;
            $data->description = str_replace('\n', "\n", $request->description);
            $data->on_linkedin = 1;

            $errors = [];
            $mediaPaths = [];
            $mediaSizes = [];

            if ($request->media) {
                foreach ($request->media as $media) {
                    $mediaName = time() . '_' . rand(1000, 9999) . '.' . $media->getClientOriginalExtension();
                    $mediaSize = $media->getSize();
                    $media->move(public_path('posts'), $mediaName);
                    $mediaPath = 'posts/' . $mediaName;

                    $mediaPaths[] = $mediaPath;
                    $mediaSizes[] = $mediaSize;
                }

                $data->media = implode(',', $mediaPaths);
                $data->media_type = $request->media_type;
            } else {
                $oldPost = Post::where('id', $request->post_id)->first();

                if ($oldPost->media != null) {
                    foreach (explode(',', $oldPost->media) as $media) {

                        $mediaName = time() . '.' . pathinfo($oldPost->media, PATHINFO_EXTENSION);
                        $mediaPath = 'posts/' . $mediaName;
                        copy(public_path($oldPost->media), public_path('posts') . '/' . $mediaName);
                        $mediaSize = filesize(public_path($mediaPath));

                        $mediaPaths[] = $mediaPath;
                        $mediaSizes[] = $mediaSize;
                    }

                    $data->media = implode(',', $mediaPaths);
                    $data->media_type = $oldPost->media_type;
                }
            }

            if ($request->schedule_date != null && $request->schedule_time != null) {
                $ip = $request->ip();
                $countryAndTimezone = getCountryAndTimezone($ip);
                $time = convertTimeToUtc($request->schedule_time, $countryAndTimezone['timezone']);
                $data->scheduled_at = $request->schedule_date . ' ' . $time;
            } else {
                if ($request->hasFile('media')) {
                    if ($data->media_type == 'image') $posted = $this->linkedinService->individualPostImage(
                        $data->media,
                        $request->description
                    );
                    if ($data->media_type == 'video') $posted = $this->linkedinService->individualPostVideo(
                        $data->media,
                        $request->description
                    );
                } else {
                    $posted = $this->linkedinService->individualPostText($request->description);
                }
                $data->posted = 1;
            }

            $data->save();

            $oldPost = Post::where('id', $request->post_id)->first();
            if ($oldPost->draft == 1) {
                if ($oldPost->media != null) if (file_exists(public_path($oldPost->media))) unlink(public_path($oldPost->media));
                $oldPost->delete();
            }

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

    /**
     * Post Edit
     */
    public function edit(String $id, String $action)
    {
        $post = Post::where('user_id', Auth::guard('web')->user()->id)->where('id', $id)->first();
        return view('user.individual-post.edit', compact('post', 'action'));
    }

    /**
     * Post Delete
     */
    public function destroy(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'post_id' => 'required|exists:posts,id',
                ],
                [
                    'post_id.required' => 'Invalid Request',
                    'post_id.exists' => 'Invalid Request',
                ]
            );

            if ($validator->fails()) throw new Exception($validator->errors()->first());

            $user = Auth::guard('web')->user();
            $data = Post::where('user_id', $user->id)->where('id', $request->post_id)->first();
            if ($data->media != null) if (file_exists(public_path($data->media))) unlink(public_path($data->media));
            $data->delete();

            return response()->json([
                'status' => 200,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage()
            ]);
        }
    }
}
