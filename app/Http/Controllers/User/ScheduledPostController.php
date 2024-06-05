<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
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
        return view($this->view);
    }


    public function all(Request $request)
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
}
