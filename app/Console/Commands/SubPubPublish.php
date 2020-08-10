<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class SubPubPublish extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subpub:publish {topic} {message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to publish messages to subscribers';

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
        $message = $this->argument('message');
        if (empty($message))
        {
            throw new Exception("The message to publish is required!");
        }
        // get all listeners for this channel
        $listeners = Redis::get($topic);
        $listeners = json_decode($listeners);
        if (empty($listeners))
        {
            $listeners = [];
        }
        foreach ($listeners as $listener)
        {
            Http::post($listener, [
                'topic' => $topic,
                'data' => ['message' => $message,]
            ]);
        }
        return 0;
    }
}
