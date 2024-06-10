<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Jobs\ScheduledPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Models\Post;

class ScheduledPostController extends Controller
{
    protected $view = 'user.post.scheduled';
    protected $route = 'user.post.scheduled';


    public function index()
    {
        if (!Auth::guard('web')->user()->can('scheduled_post')) abort(403);
        return view($this->view);
    }


    public function all(Request $request)
    {
        try {
            if (!Auth::guard('web')->user()->can('scheduled_post')) abort(403);

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

    public function job()
    {
        $posts = Post::where('posted', 0)->where('draft', 0)->where('scheduled_at', '<=', now())->get();
        ScheduledPost::dispatch();
        return $posts;
    }
}
