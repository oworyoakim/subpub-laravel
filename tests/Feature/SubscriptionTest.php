<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    private $topic = 'topic2';
    public function setUp(): void
    {
        parent::setUp();
        // Delete any previously set keys on redis
        Redis::del($this->topic);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        // Delete any set keys on redis after the test
        Redis::del($this->topic);
    }

    /**
     * @test
     */
    public function a_client_can_subscribe()
    {
        $data = [
            'url' => 'http://127.0.0.1:8001/api/event',
        ];
        $response = $this->post("/api/subscribe/{$this->topic}", $data);
        $response->assertStatus(201);
        $response->assertSeeText("Subscribed to channel {$this->topic}!");
        $listeners = Redis::get($this->topic);
        $listeners = json_decode($listeners);
        $sentListener = array_values($data);
        $this->assertEquals($sentListener, $listeners);
    }
}
