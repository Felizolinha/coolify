<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Queue extends Command
{
    protected $signature = 'start:queue';

    protected $description = 'Start 1 Queue Worker';

    public function handle()
    {
        if (config('constants.horizon.is_queue_enabled')) {
            $this->call('queue:work', [
                'redis',
                '--queue' => 'high,default',
                '--max-jobs' => 500,
                '--max-time' => 3560,
                '-v',
            ]);
            exit(0);
        } else {
            exit(0);
        }
    }
}
