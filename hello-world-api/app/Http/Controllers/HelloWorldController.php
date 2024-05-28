<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class HelloWorldController extends Controller
{
    public function helloWorld(Request $request)
    {
        // Increment request count metric
        Cache::increment('request_count');
        $count = Cache::get('request_count', 0);

        // creating new span from globally defined tracer;
        $span = $GLOBALS["tracer"]
                ->spanBuilder('handling request to helloworld api')
                ->startSpan();

        // adding request count event to span
        $span->addEvent('request_count', ['count' => $count]);
        $span -> end();
        
        // Your existing response
        return response()->json(['message' => 'hello world on request ' . $count ], 200);
    }
}
