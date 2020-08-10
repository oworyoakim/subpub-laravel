<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class SubPubSubscribe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subpub:subscribe {topic} {url}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to subscribe to a subpub topic';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $topic = $this->argument('topic');
        if (empty($topic))
        {
            throw new Exception("The topic is required!");
        }
        $url = $this->argument('url');
        if (empty($url))
        {
            throw new Exception("The publish url is required!");
        }
        // get all listeners for this channel
        $listeners = Redis::get($topic);
        $listeners = json_decode($listeners);
        if (empty($listeners))
        {
            $listeners = [];
        }
        // if this listener is not in the list, add it
        if (!in_array($url, $listeners))
        {
            $listeners[] = $url;
        }
        // store the updated list of listeners for future use
        Redis::set($topic, json_encode($listeners));

        return 0;
    }
}
