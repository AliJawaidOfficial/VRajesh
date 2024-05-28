<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\ScheduledPost;
use App\Models\Post;
use App\Services\FacebookService;
use App\Services\LinkedInService;
use Exception;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    protected $linkedinService;
    protected $facebookService;

    /**
     * Create a new job instance.
     */
    // public function __construct(LinkedInService $linkedin_service, FacebookService $facebook_service)
    // {
    //     $this->linkedinService = $linkedin_service;
    //     $this->facebookService = $facebook_service;
    // }

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        $schedule->command('app:post-scheduled')->cron('* * * * *');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
