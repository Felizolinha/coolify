<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Queue1 extends Command
{
    protected $signature = 'start:queue1';

    protected $description = 'Start 1 Queue Worker';

    public function handle()
    {
        if (config('constants.horizon.is_queue_enabled')) {
            $this->call('queue:work', [
                'connection' => 'redis',
                '--queue' => 'default',
                '--tries' => 1,
                '--backoff' => 30,
                '--max-jobs' => 200,
                '--max-time' => 3600,
                '--memory' => 128,
                '--sleep' => 5,
                '--timeout' => 60,
                '--name' => 'worker-1',
                '--verbose',
            ]);
            exit(0);
        } else {
            exit(0);
        }
    }
}
