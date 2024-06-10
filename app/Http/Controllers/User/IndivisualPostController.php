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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class IndivisualPostController extends Controller
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
                $q->where('on_linkedin', 1)->where('linkedin_company_id', null);
                $q->orWhere('on_facebook', 1)->where('facebook_page_id', null);
                $q->orWhere('on_instagram', 1)->where('instagram_account_id', null);
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
                $q->where('on_linkedin', 1)->where('linkedin_company_id', null);
                $q->orWhere('on_facebook', 1)->where('facebook_page_id', null);
                $q->orWhere('on_instagram', 1)->where('instagram_account_id', null);
            })
            ->where('posted', 1)
            ->where('draft', 0)
            ->orderBy('created_at', 'DESC');

        if ($request->has('month') && $request->month != '') $dataSet = $dataSet->whereYear('created_at', '=', Carbon::parse($request->month)->year)
            ->whereMonth('created_at', '=', Carbon::parse($request->month)->month);

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
    public function create()
    {
        return view('user.individual-post.create');
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
                    'media' => 'nullable|required_if:on_instagram,1',
                    'on_facebook' => 'nullable|boolean',
                    'on_instagram' => 'nullable|boolean',
                    'on_linkedin' => 'nullable|boolean',
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

            if ($validator->fails()) throw new Exception($validator->errors()->first());

            if (!$request->has('on_facebook') && !$request->has('on_instagram') && !$request->has('on_linkedin')) throw new Exception('Please select at least one platform.');

            $data = new Post;
            $data->user_id = Auth::guard('web')->user()->id;
            $data->title = $request->title;
            $data->description = str_replace('\n', "\n", $request->description);

            $mediaType = null;
            $media_type = null;

            $errors = [];
            if ($request->hasFile('media')) {
                $media = $request->file('media');
                $mediaName = time() . '.' . $media->getClientOriginalExtension();
                $mediaType = $media->getMimeType();
                $mediaSize = $media->getSize();
                $media->move(public_path('posts'), $mediaName);
                $onlyMediaPath = 'posts/' . $mediaName;
                $mediaPath = public_path('posts') . '/' . $mediaName;

                if (str_starts_with($mediaType, 'image/')) $media_type = 'image';
                if (str_starts_with($mediaType, 'video/')) $media_type = 'video';

                $data->media = $onlyMediaPath;
                $data->media_type = $media_type;
            }

            if ($request->schedule_date != null && $request->schedule_time != null) {
                $data->scheduled_at = $request->schedule_date . ' ' . $request->schedule_time;
                if ($request->has('on_linkedin')) $data->on_linkedin = 1;
            } else {
                $assets = env('APP_URL') . '/';

                // On Linkedin
                if ($request->has('on_linkedin')) {
                    if ($request->hasFile('media')) {
                        if ($media_type == 'image') $post = $this->linkedinService->individualPostImage($assets . $data->media, $request->description);
                        if ($media_type == 'video') $post = $this->linkedinService->individualPostVideo($assets . $data->media, $request->description);
                    } else {
                        $post = $this->linkedinService->individualPostText($request->description);
                    }
                    $data->on_linkedin = 1;
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
            $data->title = $request->title;
            $data->description = str_replace('\n', "\n", $request->description);

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

                if (str_starts_with($mediaType, 'image/')) $media_type = 'image';
                if (str_starts_with($mediaType, 'video/')) $media_type = 'video';

                $data->media = $onlyMediaPath;
                $data->media_type = $media_type;
            } else {
                $oldPost = Post::where('id', $request->post_id)->first();

                if ($oldPost->media != null) {
                    $mediaName = time() . '.' . pathinfo($oldPost->media, PATHINFO_EXTENSION);
                    $onlyMediaPath = 'posts/' . $mediaName;
                    copy(public_path($oldPost->media), public_path('posts') . '/' . $mediaName);

                    $mediaSize = filesize(public_path($onlyMediaPath));

                    $media_type = $oldPost->media_type;
                    $data->media = $onlyMediaPath;
                    $data->media_type = $media_type;
                }
            }

            // On Facebook
            if ($request->has('on_facebook')) {
                $facebook_page = explode(' - ', $request->facebook_page);
                $facebook_page_id = $facebook_page[0];
                $facebook_page_access_token = $facebook_page[1];
                $facebook_page_name = $facebook_page[2];

                $long_facebook_access_token = $this->facebookService->tokenTime($facebook_page_access_token);

                $data->on_facebook = 1;
                $data->facebook_page_id = $facebook_page_id;
                $data->facebook_page_access_token = $long_facebook_access_token;
                $data->facebook_page_name = $facebook_page_name;
            }

            // On Instagram
            if ($request->has('on_instagram')) {
                $instagram_account = explode(' - ', $request->instagram_account);
                $instagram_account_id = $instagram_account[0];
                $instagram_account_name = $instagram_account[1];

                $data->on_instagram = 1;
                $data->instagram_account_id = $instagram_account_id;
                $data->instagram_account_name = $instagram_account_name;
            }

            // On Linkedin
            if ($request->has('on_linkedin')) {
                $linkedin_organization = explode(' - ', $request->linkedin_organization);
                $linkedin_organization_id = $linkedin_organization[0];
                $linkedin_organization_name = $linkedin_organization[1];

                $data->on_linkedin = 1;
                $data->linkedin_company_id = $linkedin_organization_id;
                $data->linkedin_company_name = $linkedin_organization_name;
            }

            if ($request->schedule_date != null && $request->schedule_time != null) {
                $data->scheduled_at = $request->schedule_date . ' ' . $request->schedule_time;
            } else {
                $assets = env('APP_URL') . '/';

                // On Instagram
                if ($request->has('on_instagram')) {
                    if ($request->hasFile('media')) {
                        if ($media_type == 'image') $this->instagramService->postImage($data->instagram_account_id, $assets . $data->media, $request->description);
                        if ($media_type == 'video') $this->instagramService->postVideo($data->instagram_account_id, $assets . $data->media, $mediaSize, $request->description);
                    }
                }

                // On Facebook
                if ($request->has('on_facebook')) {
                    if ($request->hasFile('media')) {
                        if ($media_type == 'image') $this->facebookService->postImage($data->facebook_page_id, $data->facebook_page_access_token, $assets . $data->media, $request->description);
                        if ($media_type == 'video') $this->facebookService->postVideo($data->facebook_page_id, $data->facebook_page_access_token, $mediaSize, $assets . $data->media, $request->description);
                    } else {
                        $this->facebookService->postText($data->facebook_page_id, $data->facebook_page_access_token, $request->description);
                    }
                }

                // On Linkedin
                if ($request->has('on_linkedin')) {
                    if ($request->hasFile('media')) {
                        if ($media_type == 'image') $this->linkedinService->individualPostImage($assets . $data->media, $request->description);
                        if ($media_type == 'video') $this->linkedinService->individualPostVideo($assets . $data->media, $request->description);
                    } else {
                        $this->linkedinService->individualPostText($request->description);
                    }
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