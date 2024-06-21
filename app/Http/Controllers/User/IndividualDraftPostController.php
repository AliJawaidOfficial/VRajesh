<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Services\FacebookService;
use App\Services\InstagramService;
use App\Services\LinkedInService;
use App\Models\Post;
use App\Models\User;

class IndividualDraftPostController extends Controller
{
    protected $view = 'user.individual-post.draft';
    protected $route = 'user.individual-post.draft';

    protected $linkedinService;
    protected $facebookService;
    protected $instagramService;

    public function __construct(
        private readonly LinkedInService $importLinkedinService,
        private readonly InstagramService $importinstagramService,
        private readonly FacebookService $importFacebookService,
    ) {
        $this->linkedinService = $importLinkedinService;
        $this->facebookService = $importFacebookService;
        $this->instagramService = $importinstagramService;
    }

    public function index(Request $request)
    {
        $user = Auth::guard('web')->user();

        if (!Auth::guard('web')->user()->can('draft_post')) abort(403);

        $postMonths = Post::where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('on_linkedin', 1)->whereNull('linkedin_company_id');
                $q->orWhere('on_facebook', 1)->whereNull('facebook_page_id');
                $q->orWhere('on_instagram', 1)->whereNull('instagram_account_id');
            })
            ->where('posted', 0)
            ->where('draft', 1)
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
            ->where('posted', 0)
            ->where('draft', 1)
            ->orderBy('created_at', 'DESC');

        if ($request->has('month') && $request->month != '') $dataSet = $dataSet->whereYear('created_at', '=', Carbon::parse($request->month)->year)
            ->whereMonth('created_at', '=', Carbon::parse($request->month)->month);

        $dataSet = $dataSet->paginate(10);

        return view($this->view, compact('dataSet', 'postMonths'));
    }

    public function store(Request $request)
    {
        try {
            if (!Auth::guard('web')->user()->can('draft_post')) abort(403);

            $validator = Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'description' => 'nullable|required_without:media',
                    'media_type' => 'nullable|required_if:media,1|in:image,video',
                    'media' => 'nullable|required_if:on_instagram,1|max:524288',
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
            $data->description = $request->title . '\n' . str_replace('\n', '\n', $request->description);
            $data->draft = 1;

            $errors = [];

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

            // On Facebook
            if ($request->has('on_facebook')) $data->on_facebook = 1;

            // On Instagram
            if ($request->has('on_instagram')) $data->on_instagram = 1;

            // On Linkedin
            if ($request->has('on_linkedin')) $data->on_linkedin = 1;

            $data->save();

            return response()->json([
                'status' => 200,
                'message' => 'Post saved as draft successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function storeAsDraft(Request $request)
    {
        try {
            if (!Auth::guard('web')->user()->can('draft_post')) abort(403);

            $validator = Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'description' => 'nullable|required_without:media',
                    'media_type' => 'nullable|required_if:media,1|in:image,video',
                    'media' => 'nullable|max:524288',
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
            $data->post_id = $request->post_id;
            $data->title = $request->title;
            $data->description = str_replace('\n', "\n", $request->description);
            $data->draft = 1;


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

            // On Linkedin
            if ($request->has('on_linkedin')) $data->on_linkedin = 1;

            $data->save();

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Post saved as draft successfully'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request)
    {
        try {
            if (!Auth::guard('web')->user()->can('draft_post')) abort(403);

            $validator = Validator::make(
                $request->all(),
                [
                    'post_id' => 'required|exists:posts,id',
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
                    'post_id.required' => 'Invalid Request',
                    'post_id.exists' => 'Invalid Request',

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

            $oldPost = Post::where('id', $request->post_id)->first();
            if ($oldPost->media != null) {
                foreach (explode(',', $oldPost->media) as $media) {
                    if (file_exists(public_path($media))) {
                        unlink(public_path($media));
                    }
                }
                $oldPost->media = null;
                $oldPost->media_type =  null;
                $oldPost->save();
            }

            $data = Post::where('id', $request->post_id)->first();
            if (!$data) throw new Exception('Post not found.');

            $data->user_id = Auth::guard('web')->user()->id;
            $data->title = $request->title;
            $data->description = str_replace('\n', "\n", $request->description);
            $data->draft = 1;

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
            } else {
                $data->on_facebook = 0;
                $data->facebook_page_id = null;
                $data->facebook_page_access_token = null;
                $data->facebook_page_name = null;
            }

            // On Instagram
            if ($request->has('on_instagram')) {
                $instagram_account = explode(' - ', $request->instagram_account);
                $instagram_account_id = $instagram_account[0];
                $instagram_account_name = $instagram_account[1];

                $data->on_instagram = 1;
                $data->instagram_account_id = $instagram_account_id;
                $data->instagram_account_name = $instagram_account_name;
            } else {
                $data->on_instagram = 0;
                $data->instagram_account_id = null;
                $data->instagram_account_name = null;
            }

            // On Linkedin
            if ($request->has('on_linkedin')) {
                $linkedin_organization = explode(' - ', $request->linkedin_organization);
                $linkedin_organization_id = $linkedin_organization[0];
                $linkedin_organization_name = $linkedin_organization[1];

                $data->on_linkedin = 1;
                $data->linkedin_company_id = $linkedin_organization_id;
                $data->linkedin_company_name = $linkedin_organization_name;
            } else {
                $data->on_linkedin = 0;
                $data->linkedin_company_id = null;
                $data->linkedin_company_name = null;
            }

            $data->save();

            Session::flash('success', ['text' => 'Post updated successfully']);

            return response()->json([
                'status' => 200,
                'message' => 'Post updated successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => $e->getMessage()
            ]);
        }
    }
}
