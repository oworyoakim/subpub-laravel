<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SubPubController extends Controller
{
    public function subscribe(Request $request, $topic)
    {
        try
        {
            $url = $request->get('url');
            if (empty($url))
            {
                return response()->json("Listener url required in body!", 403);
            }
            Artisan::call('subpub:subscribe',[
                'topic' => $topic,
                'url' => $url,
            ]);
            return response()->json("Subscribed to channel {$topic}!", 201);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), 403);
        }
    }

    public function publish(Request $request, $topic)
    {
        try
        {
            $message = $request->get('message');
            if (empty($message))
            {
                return response()->json("The message to publish is required in body!", 403);
            }
            Artisan::call('subpub:publish',[
                'topic' => $topic,
                'message' => $message,
            ]);
            return response()->json("Published new message to channel {$topic}!", 201);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), 403);
        }
    }

    public function processEvent(Request $request)
    {
        try
        {
            $topic = $request->get('topic');
            $data = $request->get('data');
            $message = !empty($data['message']) ? $data['message'] : null;
            // log the message
            Log::info("{$topic}: {$message}");
            return response()->json("Message received on {$topic}!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), 403);
        }
    }

}
