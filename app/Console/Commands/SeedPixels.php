<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class SeedPixels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pixels:seed';

    const WIDTH = 100;
    const HEIGHT = 100;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to seed redis db with random pixel data.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        for ($x = 0; $x < self::WIDTH; $x++ ) {
            for ($y = 0; $y < self::HEIGHT; $y++) {
                Redis::set("{$x}:{$y}", rand(0,7));
            }
        }
    }
}
