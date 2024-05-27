<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
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
     * Execute the console command.
     */
    public function handle()
    {
        $url = route('post.scheduled.post');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $this->error('Request to ' . $url . ' failed. Error: ' . curl_error($ch));
        } else {
            if ($httpCode == 200) {
                $this->info('Request to ' . $url . ' was successful.');
            } else {
                $this->error('Request to ' . $url . ' failed. Status: ' . $httpCode);
            }
        }

        curl_close($ch);
    }
}
