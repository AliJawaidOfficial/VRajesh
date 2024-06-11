<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Post;
use App\Services\FacebookService;
use App\Services\InstagramService;
use App\Services\LinkedInService;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ScheduledPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Scheduled Job running...');
        $posts = Post::where('posted', 0)->where('draft', 0)->where('scheduled_at', '<=', now())->get();

        foreach ($posts as $post) {
            try {
                if ($post->on_linkedin == 1) $this->processLinkedinPost($post);
                if ($post->on_facebook == 1) $this->processFacebookPost($post);
                if ($post->on_instagram == 1) $this->processInstagramPost($post);

                Post::where('id', $post->id)->update(['posted' => 1]);
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }
        Log::info('Scheduled Job completed...');
    }



    /**
     * Linkedin Post
     */
    public function processLinkedinPost($post)
    {
        $organizationId = $post->linkedin_company_id;
        $description = $post->description;
        $user_id = $post->user_id;
        $media = $post->media;
        $media_type = $post->media_type;

        $service = new LinkedInService();
        if ($media == null) {
            $service->postText($organizationId, $description, $user_id);
        } else {
            $media = public_path($media);
            if (File::exists($media)) {
                $media_type = File::mimeType($media);
                if ($media_type == 'image') $posted = $service->postImage($organizationId, $media, $description, $user_id);
                if ($media_type == 'video') $posted = $service->postVideo($organizationId, $media, $description, $user_id);
                Log::info($posted);
            }
        }
    }



    /**
     * Facebook Post
     */
    public function processFacebookPost($post)
    {
        $pageId = $post->facebook_page_id;
        $pageAccessToken = $post->facebook_page_access_token;
        $description = $post->description;
        $userId = $post->user_id;
        $media = $post->media;
        $media_type = $post->media_type;

        $service = new FacebookService();
        if ($media == null) {
            $service->postText($pageId, $pageAccessToken, $description, $userId);
        } else {
            $media = public_path($media);

            if (file_exists($media)) {
                $media_size = File::size($media);

                if ($media_type == 'image') $posted = $service->postImage($pageId, $pageAccessToken, $media, $description, $userId);
                if ($media_type == 'video') $posted = $service->postVideo($pageId, $pageAccessToken, $media_size, $media, $description, $userId);
                Log::info($posted);
            }
        }
    }



    /**
     * Instagram Post
     */
    public function processInstagramPost($post)
    {
        $igUserId = $post->instagram_account_id;
        $description = $post->description;
        $userId = $post->user_id;
        $media = $post->media;
        $media_type = $post->media_type;

        $service = new InstagramService();
        if ($media != null) {
            $media = public_path($media);
            if (File::exists($media)) {
                $media_size = File::size($media);

                if ($media_type == 'image') $posted = $service->postImage($igUserId, $media, $description, $userId);
                if ($media_type == 'video') $posted = $service->postVideo($igUserId, $media, $media_size, $description, $userId);
                Log::info($posted);
            }
        }
    }
}
