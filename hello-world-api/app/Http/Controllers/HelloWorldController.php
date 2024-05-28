<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenTelemetry\SDK\Trace\TracerProvider;


class HelloWorldController extends Controller
{
    public function helloWorld(Request $request)
    {
        // Increment request count metric
        //$tracer = Tracer::get();
        //$tracer = TracerProvider::getDefaultTracer();
        $tracerProvider = new TracerProvider();
        $tracer = $tracerProvider->getTracer('io.opentelemetry.contrib.php');
        $tracer
            ->spanBuilder('example')
            ->startSpan()
            ->end();
        //$tracer->getCurrentSpan()->addEvent('request_count', ['count' => 1]);

        // Your existing response
        return response()->json(['message' => 'hello world'], 200);
    }
}
