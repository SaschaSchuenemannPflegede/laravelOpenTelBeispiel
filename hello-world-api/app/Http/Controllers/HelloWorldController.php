<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenTelemetry\SDK\Trace\TracerProvider;
use Illuminate\Support\Facades\Cache;


class HelloWorldController extends Controller
{
    public function helloWorld(Request $request)
    {
        // Increment request count metric
        Cache::increment('request_count');
        //$tracer = Tracer::get();
        //$tracer = TracerProvider::getDefaultTracer();
        //$tracerProvider = new TracerProvider();
        //$tracer = $tracerProvider->getTracer('io.opentelemetry.contrib.php');
        //$span = $tracer
        //    ->spanBuilder('example')
        //    ->startSpan()
        //    ->end();
        $count = Cache::get('request_count', 0);
        $span = $GLOBALS["tracer"]
                ->spanBuilder('handling request to helloworld api')
                ->startSpan();
        $span->addEvent('request_count', ['count' => $count]);
        $span -> end();
        // Your existing response
        return response()->json(['message' => 'hello world on request ' . $count ], 200);
    }
}
