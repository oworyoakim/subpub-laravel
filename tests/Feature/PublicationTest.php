<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class PublicationTest extends TestCase
{
    use WithFaker;
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
     * This test should be run when the client/listener is running
     * @test
     */
    public function a_message_can_be_published_to_clients()
    {
        $data = [
            'url' => 'http://127.0.0.1:8001/api/event',
        ];
        // Send a subscription request
        $this->post("/api/subscribe/{$this->topic}", $data);
        $body = [
            'message' => $this->faker->sentence
        ];
        $response = $this->post("/api/publish/{$this->topic}", $body);
        $response->assertStatus(201);
        $response->assertSeeText("Published new message to channel {$this->topic}!");
    }
}
