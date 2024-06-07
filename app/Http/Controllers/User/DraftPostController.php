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

class DraftPostController extends Controller
{
    protected $view = 'user.post.draft';
    protected $route = 'user.post.draft';

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

        return view($this->view, compact('dataSet', 'postMonths'));
    }

    public function store(Request $request)
    {
        try {
            if (!Auth::guard('web')->user()->can('draft_post')) abort(403); 

            DB::beginTransaction();

            $validator = Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'description' => 'nullable|required_without:media',
                    'media' => 'nullable',
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
                    'media.required_if' => 'Media is required',

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
            $data->title = $request->title;
            $data->description = str_replace('\n', "\n", $request->description);
            $data->draft = 1;

            $mediaType = null;
            $media_type = null;

            if ($request->hasFile('media')) {
                $media = $request->file('media');
                $mediaName = time() . '.' . $media->getClientOriginalExtension();
                $mediaType = $media->getMimeType();
                $media->move(public_path('posts'), $mediaName);
                $onlyMediaPath = 'posts/' . $mediaName;

                if (str_starts_with($mediaType, 'image/')) $media_type = 'image';
                if (str_starts_with($mediaType, 'video/')) $media_type = 'video';

                $data->media = $onlyMediaPath;
                $data->media_type = $media_type;
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

            if ($request->schedule_date != null && $request->schedule_time != null) $data->scheduled_at = $request->schedule_date . ' ' . $request->schedule_time;

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


    public function storeAsDraft(Request $request)
    {
        try {
            if (!Auth::guard('web')->user()->can('draft_post')) abort(403); 

            DB::beginTransaction();

            $validator = Validator::make(
                $request->all(),
                [
                    'post_id' => 'required|exists:posts,id',
                    'title' => 'required',
                    'description' => 'nullable|required_without:media',
                    'media' => 'nullable',
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
                    'post_id.required' => 'Invalid request.',
                    'post_id.exists' => 'Invalid request.',

                    'title.required' => 'Title is required',

                    'description.required' => 'Description is required',
                    'description.required_without' => 'Description is required',

                    'media.required' => 'Media is required',
                    'media.required_if' => 'Media is required',

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
            $data->title = $request->title;
            $data->description = str_replace('\n', "\n", $request->description);
            $data->draft = 1;

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
                    copy(public_path($oldPost->media), public_path('posts') . '/' . $mediaName);
                    $onlyMediaPath = 'posts/' . $mediaName;

                    $data->media = $onlyMediaPath;
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

            DB::beginTransaction();

            $validator = Validator::make(
                $request->all(),
                [
                    'post_id' => 'required|exists:posts,id',
                    'title' => 'required',
                    'description' => 'nullable|required_without:media',
                    'media' => 'nullable',
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
                    'post_id.required' => 'Invalid request.',
                    'post_id.exists' => 'Invalid request.',

                    'title.required' => 'Title is required',

                    'description.required' => 'Description is required',
                    'description.required_without' => 'Description is required',

                    'media.required' => 'Media is required',
                    'media.required_if' => 'Media is required',

                    'facebook_page.required_if' => 'Facebook Page is required.',

                    'instagram_account.required_if' => 'Instagram Account is required.',

                    'linkedin_organization.required_if' => 'Linkedin Organization is required.',

                    'schedule_date.after_or_equal' => 'Schedule date should be a future date.',

                    'schedule_time.after_or_equal' => 'Schedule time should be a future time.',
                ]
            );

            if ($validator->fails()) throw new Exception($validator->errors()->first());

            if (!$request->has('on_facebook') && !$request->has('on_instagram') && !$request->has('on_linkedin')) throw new Exception('Please select at least one platform.');

            $data = Post::where('id', $request->post_id)->first();
            if (!$data) throw new Exception('Post not found.');

            $data->user_id = Auth::guard('web')->user()->id;
            $data->title = $request->title;
            $data->description = str_replace('\n', "\n", $request->description);
            $data->draft = 1;

            $mediaType = null;
            $media_type = null;
            
            if ($request->hasFile('media')) {
                $media = $request->file('media');
                $mediaName = time() . '.' . $media->getClientOriginalExtension();
                $mediaType = $media->getMimeType();
                $media->move(public_path('posts'), $mediaName);
                $onlyMediaPath = 'posts/' . $mediaName;

                if (str_starts_with($mediaType, 'image/')) $media_type = 'image';
                if (str_starts_with($mediaType, 'video/')) $media_type = 'video';

                $data->media = $onlyMediaPath;
                $data->media_type = $media_type;
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

            DB::commit();

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
