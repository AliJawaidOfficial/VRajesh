<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Services\FacebookService;
use App\Services\LinkedInService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class PostScheduled extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:post-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * 
     * @var App\Services\LinkedInService
     */
    protected $linkedinService;

    /**
     * 
     * @var App\Services\FacebookService
     */
    protected $facebookService;


    /**
     * Execute the console command.
     */

    public function __construct(private readonly LinkedInService $importLinkedinService, private readonly FacebookService $importFacebookService)
    {
        parent::__construct();
        $this->linkedinService = $importLinkedinService;
        $this->facebookService = $importFacebookService;
    }

    public function handle()
    {
        $this->info(now());
        $posts = Post::where('scheduled_at', '<=', now())
            ->where('posted', 0)
            ->where('draft', 0)
            ->get();

        foreach ($posts as $post) {

            if ($post->on_facebook == 1) {
                if ($post->media != null) {
                    $media_path = public_path($post->media);
                    $media_size = filesize($post->media);
                    if ($post->media_type == 'image') $this->facebookService->postImage($media_path, $post->description, $post->user_id);
                    if ($post->media_type == 'video') $this->facebookService->postVideo($media_size, $media_path, $post->description, $post->user_id);
                } else {
                    $this->facebookService->postText($post->description, $post->user_id);
                }
            }

            if ($post->on_linkedin == 1) {
                if ($post->media != null) {
                    $media_path = public_path($post->media);
                    if ($post->media_type == 'image') $this->linkedinService->postImage($media_path, $post->description, $post->user_id);
                    if ($post->media_type == 'video') $this->linkedinService->postVideo($media_path, $post->description, $post->user_id);
                } else {
                    $this->linkedinService->postText($post->description, $post->user_id);
                }
            }

            Post::where('id', $post->id)->update(['posted' => 1]);
        }

        $this->info('Posts have been posted successfully');
    }
}
