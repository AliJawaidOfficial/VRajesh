<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Post;
use App\Services\FacebookService;
use App\Services\LinkedInService;
use Exception;
use Illuminate\Support\Facades\Log;

class ScheduledPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $linkedinService;
    protected $facebookService;

    /**
     * Create a new job instance.
     */
    public function __construct(LinkedInService $linkedin_service, FacebookService $facebook_service)
    {
        $this->linkedinService = $linkedin_service;
        $this->facebookService = $facebook_service;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $posts = Post::where('scheduled_at', '<=', now())->where('posted', 0)->where('draft', 0)->get();

        foreach ($posts as $post) {
            try {
                if ($post->on_facebook == 1) $this->processFacebookPost($post);
                if ($post->on_linkedin == 1) $this->processLinkedinPost($post);
                if ($post->on_instagram == 1) $this->processInstagramPost($post);
            } catch (Exception $e) {
                Log::error('Failed to process post ID ' . $post->id . ': ' . $e->getMessage());
            }
            Post::where('id', $post->id)->update(['posted' => 1]);
        }
    }

    protected function processFacebookPost($post)
    {
        if ($post->media) {
            $media_path = public_path($post->media);
            $media_size = filesize($post->media);
            if ($post->media_type == 'image') $this->facebookService->postImage($media_path, $post->description);
            if ($post->media_type == 'video') $this->facebookService->postVideo($media_size, $media_path, $post->description);
        } else {
            $this->facebookService->postText($post->description);
        }
    }

    protected function processInstagramPost($post)
    {
        if ($post->media) {
            $media_path = public_path($post->media);
            $media_size = filesize($post->media);
            // if ($post->media_type == 'image') $this->facebookService->postImage($post->media, $post->description);
            // if ($post->media_type == 'video') $this->facebookService->postVideo($media_size, $post->media, $post->description);
        } else {
            // $this->facebookService->postText($post->description);
        }
    }

    protected function processLinkedinPost($post)
    {
        if ($post->media) {
            $media_path = public_path($post->media);
            if ($post->media_type == 'image') $this->linkedinService->postImage($media_path, $post->description);
            if ($post->media_type == 'video') $this->linkedinService->postVideo($media_path, $post->description);
        } else {
            $this->linkedinService->postText($post->description);
        }
    }
}
