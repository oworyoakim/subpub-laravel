<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProcessEventTest extends TestCase
{
    use WithFaker;

    private $topic = 'topic2';

    /**
     * This test should be run when the client/listener is running
     * @test
     */
    public function a_client_can_receive_messages()
    {
        $listener = "http://127.0.0.1:8001/api/event";
        $data = [
            'topic' => $this->topic,
            'data' => [
                'message' => $this->faker->sentence,
            ]
        ];
        // Send a subscription request
        $response = Http::post($listener, $data);
        $this->assertEquals(200, $response->status());
        $this->assertEquals("Message received on {$this->topic}!", $response->json());
    }
}
