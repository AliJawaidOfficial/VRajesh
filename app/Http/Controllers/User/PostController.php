<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\FacebookService;
use App\Services\GoogleService;
use App\Services\InstagramService;
use App\Services\LinkedInService;
use App\Services\PixelsService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    protected $googleService;
    protected $linkedinService;
    protected $facebookService;
    protected $instagramService;
    protected $pixelsService;

    public function __construct(
        private readonly GoogleService $importGoogleService,
        private readonly LinkedInService $importLinkedinService,
        private readonly InstagramService $importinstagramService,
        private readonly FacebookService $importFacebookService,
        private readonly PixelsService $importedPixelsService,
    ) {
        $this->googleService = $importGoogleService;
        $this->linkedinService = $importLinkedinService;
        $this->facebookService = $importFacebookService;
        $this->instagramService = $importinstagramService;
        $this->pixelsService = $importedPixelsService;
    }

    /**
     * Get Facebok Pages
     */
    public function facebookPages()
    {
        $facebookPages = [];
        try {
            if (Auth::guard('web')->user()->meta_access_token != null) $facebookPages = $this->facebookService->getPages();
        } catch (Exception $e) {
        }
        return response()->json($facebookPages);
    }

    /**
     * Get Instagram Accounts
     */
    public function instagramAccounts()
    {
        $instagramPages = [];
        try {
            if (Auth::guard('web')->user()->meta_access_token != null) $instagramPages = $this->instagramService->getPages();
        } catch (Exception $e) {
        }
        return response()->json($instagramPages);
    }

    /**
     * Get LinkedIn Organizations
     */
    public function linkedinOrganizations()
    {
        $linkedinOrganizations = [];
        try {
            if (Auth::guard('web')->user()->linkedin_access_token != null) $linkedinOrganizations = $this->linkedinService->getOrganizations();
        } catch (Exception $e) {
        }
        return response()->json($linkedinOrganizations);
    }

    /**
     * Get LinkedIn Organizations
     */
    public function googleBusinessProfiles()
    {
        $googleBusinessProfiles = [];
        try {
            if (Auth::guard('web')->user()->google_access_token != null) $googleBusinessProfiles = $this->googleService->getBusinessProfiles();
        } catch (Exception $e) {
        }
        return response()->json($googleBusinessProfiles);
    }

    /**
     * Post Posts
     */
    public function index(Request $request)
    {
        $user = Auth::guard('web')->user();

        $postMonths = Post::where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('on_linkedin', 1)->whereNotNull('linkedin_company_id');
                $q->orWhere('on_facebook', 1)->whereNotNull('facebook_page_id');
                $q->orWhere('on_instagram', 1)->whereNotNull('instagram_account_id');
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
                $q->where('on_linkedin', 1)->whereNotNull('linkedin_company_id');
                $q->orWhere('on_facebook', 1)->whereNotNull('facebook_page_id');
                $q->orWhere('on_instagram', 1)->whereNotNull('instagram_account_id');
            })
            ->where('posted', 1)
            ->where('draft', 0)
            ->orderBy('created_at', 'DESC');

        if ($request->has('month') && $request->month != '') $dataSet = $dataSet->whereYear('created_at', '=', Carbon::parse($request->month)->year)
            ->whereMonth('created_at', '=', Carbon::parse($request->month)->month);

        $dataSet = $dataSet->select(
            'posts.*',
            DB::raw('(SELECT COUNT(*) FROM `posts` AS `post_2` WHERE `post_2`.`post_id`=`posts`.`id`) + 1 AS `post_count`'),
        );

        $dataSet = $dataSet->paginate(10);

        $timezone = getCountryAndTimezone($request->ip())['timezone'];

        return view('user.post.index', compact('dataSet', 'postMonths', 'timezone'));
    }

    /**
     * Post Details
     */
    public function show(String $id)
    {
        try {
            $user = Auth::guard('web')->user();
            $data = Post::select(
                'posts.*',
                DB::raw("DATE_FORMAT(posts.scheduled_at, '%m/%e/%Y %h:%i:%s %p') AS scheduled_at"),
                DB::raw("DATE_FORMAT(posts.created_at, '%a, %e %b %Y %h:%i %p') AS formatted_created_at"),
            )->where('user_id', $user->id)->where('id', $id)->first();

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
        if (!Auth::guard('web')->user()->canAny(['meta_facebook_text_post', 'meta_facebook_image_post', 'meta_facebook_video_post', 'meta_instagram_image_post', 'meta_instagram_video_post', 'linkedin_text_post', 'linkedin_image_post', 'linkedin_video_post'])) abort(403);
        $schedule_date = $request->has('schedule_date') ? $request->schedule_date : null;
        return view('user.post.create', compact('schedule_date'));
    }

    /**
     * Post Edit
     */
    public function edit(String $id, String $action)
    {
        $post = Post::where('user_id', Auth::guard('web')->user()->id)->where('id', $id)->first();
        return view('user.post.edit', compact('post', 'action'));
    }

    /**
     * Post Store
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'description' => 'nullable|required_without:media',
                    'media_type' => 'nullable|required_if:media,1|in:image,video',
                    'media' => 'nullable|required_if:on_instagram,1|max:524288',
                    'on_facebook' => 'nullable|boolean',
                    'facebook_page' => 'nullable|required_if:on_facebook,1',
                    'on_instagram' => 'nullable|boolean',
                    'instagram_account' => 'nullable|required_if:on_instagram,1',
                    'on_linkedin' => 'nullable|boolean',
                    'linkedin_organization' => 'nullable|required_if:on_linkedin,1',
                    'schedule_date' => 'nullable|date|after_or_equal:today',
                    'schedule_time' => 'nullable|date_format:H:i',
                ],
                [
                    'title.required' => 'Title is required',

                    'description.required' => 'Description is required',
                    'description.required_without' => 'Description is required',

                    'media.required' => 'Media is required',
                    'media.required_if' => 'Media is required for Instagram.',
                    'media.max' => 'Media size should be less than 5MB.',

                    'facebook_page.required_if' => 'Facebook Page is required.',

                    'instagram_account.required_if' => 'Instagram Account is required.',

                    'linkedin_organization.required_if' => 'Linkedin Organization is required.',

                    'schedule_date.after_or_equal' => 'Schedule date should be a future date.',

                    'schedule_time.after_or_equal' => 'Schedule time should be a future time.',

                    'media_type.in' => 'Invalid Request',
                    'media_type.required_if' => 'Invalid Request',
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

            if (!$request->has('on_facebook') && !$request->has('on_instagram') && !$request->has('on_linkedin')) throw new Exception('Please select at least one platform.');

            $data = new Post;
            $data->user_id = Auth::guard('web')->user()->id;
            $data->title = $request->title;
            $data->description = str_replace('\n', "\n", $request->description);

            $errors = [];
            $success = [];
            $mediaPaths = [];
            $mediaSizes = [];

            if ($request->media) {
                if ($request->has('on_linkedin')) {
                    if ($request->media_type == 'image' && !Auth::guard('web')->user()->can('linkedin_image_post')) throw new Exception('You do not have permission to post image on Linkedin.');
                    if ($request->media_type == 'video' && !Auth::guard('web')->user()->can('linkedin_video_post')) throw new Exception('You do not have permission to post video on Linkedin.');
                }

                if ($request->has('on_instagram')) {
                    if ($request->media_type == 'image' && !Auth::guard('web')->user()->can('meta_instagram_image_post')) throw new Exception('You do not have permission to post image on Instagram.');
                    if ($request->media_type == 'video' && !Auth::guard('web')->user()->can('meta_instagram_video_post')) throw new Exception('You do not have permission to post video on Instagram.');
                }

                if ($request->has('on_facebook')) {
                    if ($request->media_type == 'image' && !Auth::guard('web')->user()->can('meta_facebook_image_post')) throw new Exception('You do not have permission to post image on Facebook.');
                    if ($request->media_type == 'video' && !Auth::guard('web')->user()->can('meta_facebook_video_post')) throw new Exception('You do not have permission to post video on Facebook.');
                }

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

            // On Facebook
            if ($request->has('on_facebook')) {
                $facebook_page = explode(' - ', $request->facebook_page);
                $facebook_page_id = $facebook_page[0];
                $facebook_page_access_token = $facebook_page[1];
                $facebook_page_name = $facebook_page[2];

                $long_facebook_access_token = $this->facebookService->tokenTime($facebook_page_access_token);

                $data->facebook_page_id = $facebook_page_id;
                $data->facebook_page_access_token = $long_facebook_access_token;
                $data->facebook_page_name = $facebook_page_name;
            }

            // On Instagram
            if ($request->has('on_instagram')) {
                $instagram_account = explode(' - ', $request->instagram_account);
                $instagram_account_id = $instagram_account[0];
                $instagram_account_name = $instagram_account[1];

                $data->instagram_account_id = $instagram_account_id;
                $data->instagram_account_name = $instagram_account_name;
            }

            // On Linkedin
            if ($request->has('on_linkedin')) {
                $linkedin_organization = explode(' - ', $request->linkedin_organization);
                $linkedin_organization_id = $linkedin_organization[0];
                $linkedin_organization_name = $linkedin_organization[1];

                $data->linkedin_company_id = $linkedin_organization_id;
                $data->linkedin_company_name = $linkedin_organization_name;
            }

            if ($request->schedule_date != null && $request->schedule_time != null) {
                $ip = $request->ip();
                $countryAndTimezone = getCountryAndTimezone($ip);
                $time = convertTimeToUtc($request->schedule_time, $countryAndTimezone['timezone']);
                $data->scheduled_at = $request->schedule_date . ' ' . $time;
                if ($request->has('on_facebook')) $data->on_facebook = 1;
                if ($request->has('on_instagram')) $data->on_instagram = 1;
                if ($request->has('on_linkedin')) $data->on_linkedin = 1;
            } else {
                // On Instagram
                if ($request->has('on_instagram')) {
                    if ($data->media != null) {

                        if ($data->media_type == 'image')
                            $instagramPost = $this->instagramService->postImage(
                                $data->instagram_account_id,
                                $data->media,
                                $request->description
                            );

                        if ($data->media_type == 'video')
                            $instagramPost = $this->instagramService->postVideo(
                                $data->instagram_account_id,
                                explode(',', $data->media)[0],
                                $mediaSizes[0],
                                $request->description
                            );

                        if (isset($instagramPost['error'])) {
                            $errors[] = [
                                'heading' => 'Instagram',
                                'message' => $instagramPost['error'],
                            ];
                        } else {
                            $data->on_instagram = 1;
                            $success[] = [
                                'heading' => 'Instagram',
                                'message' => 'Posted successfully.'
                            ];
                        }
                    }
                }

                // On Facebook
                if ($request->has('on_facebook')) {
                    if ($data->media != null) {
                        if ($data->media_type == 'image')
                            $facebookPost = $this->facebookService->postImages(
                                $data->facebook_page_id,
                                $data->facebook_page_access_token,
                                $data->media,
                                $request->description
                            );

                        if ($data->media_type == 'video')
                            $facebookPost = $this->facebookService->postVideo(
                                $data->facebook_page_id,
                                $data->facebook_page_access_token,
                                $mediaSizes[0],
                                explode(',', $data->media)[0],
                                $request->description
                            );
                    } else {
                        $facebookPost = $this->facebookService->postText($data->facebook_page_id, $data->facebook_page_access_token, $request->description);
                    }

                    if (isset($facebookPost['error'])) {
                        $errors[] = [
                            'heading' => 'Facebook',
                            'message' => $facebookPost['error'],
                        ];
                    } else {
                        $data->on_facebook = 1;
                        $success[] = [
                            'heading' => 'Facebook',
                            'message' => 'Posted successfully.'
                        ];
                    }
                }

                // On Linkedin
                if ($request->has('on_linkedin')) {
                    if ($data->media != null) {
                        if ($data->media_type == 'image')
                            $linkedinPost = $this->linkedinService->postImage(
                                $data->linkedin_company_id,
                                $data->media,
                                $request->description
                            );

                        if ($data->media_type == 'video')
                            $linkedinPost = $this->linkedinService->postVideo(
                                $data->linkedin_company_id,
                                explode(',', $data->media)[0],
                                $request->description
                            );
                    } else {
                        $linkedinPost = $this->linkedinService->postText($data->linkedin_company_id, $request->description);
                    }

                    if (isset($linkedinPost['error'])) {
                        $errors[] = [
                            'heading' => 'LinkedIn',
                            'message' => $linkedinPost['error'],
                        ];
                    } else {
                        $data->on_linkedin = 1;
                        $success[] = [
                            'heading' => 'LinkedIn',
                            'message' => 'Posted successfully.'
                        ];
                    }
                }

                $data->posted = 1;
            }

            $data->save();

            if (count($errors) > 0) return response()->json([
                'status' => 500,
                'success' => $success,
                'errors' => $errors,
            ]);

            if ($request->schedule_date != null && $request->schedule_time != null) return response()->json([
                'status' => 200,
                'message' => 'Post scheduled successfully',
                'redirect' => route('user.post.scheduled')
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Post published successfully',
                'redirect' => route('user.post.index')
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 400,
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
            $validator = Validator::make(
                $request->all(),
                [
                    'post_id' => 'required|exists:posts,id',
                    'title' => 'required',
                    'description' => 'nullable|required_without:media',
                    'media' => 'nullable|required_if:on_instagram,1|array',
                    'media.*' => 'nullable|max:5000|mimes:jpg,jpeg,png,mp4,webm',
                    'on_facebook' => 'nullable|boolean',
                    'facebook_page' => 'nullable|required_if:on_facebook,1',
                    'on_instagram' => 'nullable|boolean',
                    'instagram_account' => 'nullable|required_if:on_instagram,1',
                    'on_linkedin' => 'nullable|boolean',
                    'linkedin_organization' => 'nullable|required_if:on_linkedin,1',
                    'schedule_date' => 'nullable|date|after_or_equal:today',
                    'schedule_time' => 'nullable|date_format:H:i',
                ],
                [
                    'post_id.required' => 'Invalid Request',
                    'post_id.exists' => 'Invalid Request',

                    'description.required' => 'Description is required',
                    'description.required_without' => 'Description is required',

                    'media.required' => 'Media is required',
                    'media.required_if' => 'Media is required for Instagram.',
                    'media.max' => 'Media size should be less than 5MB.',

                    'facebook_page.required_if' => 'Facebook Page is required.',

                    'instagram_account.required_if' => 'Instagram Account is required.',

                    'linkedin_organization.required_if' => 'Linkedin Organization is required.',

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

            $errors = [];
            $success = [];
            $mediaPaths = [];
            $mediaSizes = [];

            $errors = [];
            $mediaPaths = [];
            $mediaSizes = [];

            if ($request->media) {
                if ($request->has('on_linkedin')) {
                    if ($request->media_type == 'image' && !Auth::guard('web')->user()->can('linkedin_image_post')) throw new Exception('You do not have permission to post image on Linkedin.');
                    if ($request->media_type == 'video' && !Auth::guard('web')->user()->can('linkedin_video_post')) throw new Exception('You do not have permission to post video on Linkedin.');
                }

                if ($request->has('on_instagram')) {
                    if ($request->media_type == 'image' && !Auth::guard('web')->user()->can('meta_instagram_image_post')) throw new Exception('You do not have permission to post image on Instagram.');
                    if ($request->media_type == 'video' && !Auth::guard('web')->user()->can('meta_instagram_video_post')) throw new Exception('You do not have permission to post video on Instagram.');
                }

                if ($request->has('on_facebook')) {
                    if ($request->media_type == 'image' && !Auth::guard('web')->user()->can('meta_facebook_image_post')) throw new Exception('You do not have permission to post image on Facebook.');
                    if ($request->media_type == 'video' && !Auth::guard('web')->user()->can('meta_facebook_video_post')) throw new Exception('You do not have permission to post video on Facebook.');
                }

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

                if ($oldPost->on_linkedin == 1) {
                    if ($oldPost->media_type == 'image' && !Auth::guard('web')->user()->can('linkedin_image_post')) throw new Exception('You do not have permission to post image on Linkedin.');
                    if ($oldPost->media_type == 'video' && !Auth::guard('web')->user()->can('linkedin_video_post')) throw new Exception('You do not have permission to post video on Linkedin.');
                }

                if ($oldPost->on_instagram == 1) {
                    if ($oldPost->media_type == 'image' && !Auth::guard('web')->user()->can('meta_instagram_image_post')) throw new Exception('You do not have permission to post image on Instagram.');
                    if ($oldPost->media_type == 'video' && !Auth::guard('web')->user()->can('meta_instagram_video_post')) throw new Exception('You do not have permission to post video on Instagram.');
                }

                if ($oldPost->on_facebook == 1) {
                    if ($oldPost->media_type == 'image' && !Auth::guard('web')->user()->can('meta_facebook_image_post')) throw new Exception('You do not have permission to post image on Facebook.');
                    if ($oldPost->media_type == 'video' && !Auth::guard('web')->user()->can('meta_facebook_video_post')) throw new Exception('You do not have permission to post video on Facebook.');
                }

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

            // On Facebook
            if ($request->has('on_facebook')) {
                $facebook_page = explode(' - ', $request->facebook_page);
                $facebook_page_id = $facebook_page[0];
                $facebook_page_access_token = $facebook_page[1];
                $facebook_page_name = $facebook_page[2];

                $long_facebook_access_token = $this->facebookService->tokenTime($facebook_page_access_token);

                $data->facebook_page_id = $facebook_page_id;
                $data->facebook_page_access_token = $long_facebook_access_token;
                $data->facebook_page_name = $facebook_page_name;
            }

            // On Instagram
            if ($request->has('on_instagram')) {
                $instagram_account = explode(' - ', $request->instagram_account);
                $instagram_account_id = $instagram_account[0];
                $instagram_account_name = $instagram_account[1];

                $data->instagram_account_id = $instagram_account_id;
                $data->instagram_account_name = $instagram_account_name;
            }

            // On Linkedin
            if ($request->has('on_linkedin')) {
                $linkedin_organization = explode(' - ', $request->linkedin_organization);
                $linkedin_organization_id = $linkedin_organization[0];
                $linkedin_organization_name = $linkedin_organization[1];

                $data->linkedin_company_id = $linkedin_organization_id;
                $data->linkedin_company_name = $linkedin_organization_name;
            }

            if ($request->schedule_date != null && $request->schedule_time != null) {
                $ip = $request->ip();
                $countryAndTimezone = getCountryAndTimezone($ip);
                $time = convertTimeToUtc($request->schedule_time, $countryAndTimezone['timezone']);
                $data->scheduled_at = $request->schedule_date . ' ' . $time;
                if ($request->has('on_facebook')) $data->on_facebook = 1;
                if ($request->has('on_instagram')) $data->on_instagram = 1;
                if ($request->has('on_linkedin')) $data->on_linkedin = 1;
            } else {
                if ($request->has('on_instagram')) {
                    if ($data->media != null) {

                        if ($data->media_type == 'image')
                            $instagramPost = $this->instagramService->postImage(
                                $data->instagram_account_id,
                                $data->media,
                                $request->description
                            );

                        if ($data->media_type == 'video')
                            $instagramPost = $this->instagramService->postVideo(
                                $data->instagram_account_id,
                                explode(',', $data->media)[0],
                                $mediaSizes[0],
                                $request->description
                            );

                        if (isset($instagramPost['error'])) {
                            $errors[] = [
                                'heading' => 'Instagram',
                                'message' => $instagramPost['error'],
                            ];
                        } else {
                            $data->on_instagram = 1;
                            $success[] = [
                                'heading' => 'Instagram',
                                'message' => 'Posted successfully.'
                            ];
                        }
                    }
                }

                // On Facebook
                if ($request->has('on_facebook')) {
                    if ($data->media != null) {
                        if ($data->media_type == 'image')
                            $facebookPost = $this->facebookService->postImages(
                                $data->facebook_page_id,
                                $data->facebook_page_access_token,
                                $data->media,
                                $request->description
                            );

                        if ($data->media_type == 'video')
                            $facebookPost = $this->facebookService->postVideo(
                                $data->facebook_page_id,
                                $data->facebook_page_access_token,
                                $mediaSizes[0],
                                explode(',', $data->media)[0],
                                $request->description
                            );
                    } else {
                        $facebookPost = $this->facebookService->postText($data->facebook_page_id, $data->facebook_page_access_token, $request->description);
                    }

                    if (isset($facebookPost['error'])) {
                        $errors[] = [
                            'heading' => 'Facebook',
                            'message' => $facebookPost['error'],
                        ];
                    } else {
                        $data->on_facebook = 1;
                        $success[] = [
                            'heading' => 'Facebook',
                            'message' => 'Posted successfully.'
                        ];
                    }
                }

                // On Linkedin
                if ($request->has('on_linkedin')) {
                    if ($data->media != null) {
                        if ($data->media_type == 'image')
                            $linkedinPost = $this->linkedinService->postImage(
                                $data->linkedin_company_id,
                                $data->media,
                                $request->description
                            );

                        if ($data->media_type == 'video')
                            $linkedinPost = $this->linkedinService->postVideo(
                                $data->linkedin_company_id,
                                explode(',', $data->media)[0],
                                $request->description
                            );
                    } else {
                        $linkedinPost = $this->linkedinService->postText($data->linkedin_company_id, $request->description);
                    }

                    if (isset($linkedinPost['error'])) {
                        $errors[] = [
                            'heading' => 'LinkedIn',
                            'message' => $linkedinPost['error'],
                        ];
                    } else {
                        $data->on_linkedin = 1;
                        $success[] = [
                            'heading' => 'LinkedIn',
                            'message' => 'Posted successfully.'
                        ];
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

            if (count($errors) > 0) return response()->json([
                'status' => 500,
                'success' => $success,
                'errors' => $errors,
            ]);

            if ($request->schedule_date != null && $request->schedule_time != null) return response()->json([
                'status' => 200,
                'message' => 'Post scheduled successfully',
                'redirect' => route('user.post.scheduled')
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Post published successfully',
                'redirect' => route('user.post.index')
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 400,
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
