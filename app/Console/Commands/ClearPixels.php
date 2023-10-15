<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class SeedPixels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pixels:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to clear redis db with random pixel data.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        for ($x = 0; $x < config('settings.width'); $x++ ) {
            for ($y = 0; $y < config('settings.height'); $y++) {
                Redis::set("{$x}:{$y}", 0 . ':');
            }
        }
    }
}
