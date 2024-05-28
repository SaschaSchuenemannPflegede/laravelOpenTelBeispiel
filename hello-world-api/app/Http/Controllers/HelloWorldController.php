<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use OpenTelemetry\SDK\Metrics\MeterProvider;
use OpenTelemetry\API\Metrics\Meter;
use OpenTelemetry\API\Metrics\ObserverInterface;


class HelloWorldController extends Controller
{
    public function helloWorld(Request $request)
    {
        // Increment request count metric
        Cache::increment('request_count');
        $count = Cache::get('request_count', 0);

        // creating new span from globally defined tracer;
        /*
        $span = $GLOBALS["tracer"]
                ->spanBuilder('handling request to helloworld api')
                ->startSpan();

        // adding request count event to span
        $span->addEvent('request_count', ['count' => $count]);
        $span -> end();
        */

        
        $meterProvider = MeterProvider::builder()->addReader($GLOBALS["reader"])->build();
        $GLOBALS["meter"] = $meterProvider->getMeter('example-meter');

        $counter = $GLOBALS["meter"]
        ->createCounter('example_counter', 'An example counter metric', 'number of requests' )
        ->add($count);
        
        /*
        $GLOBALS["meter"]
            ->createObservableGauge('number', 'items', 'Random number')
            ->observe(static function (ObserverInterface $observer): void {
                $observer->observe(random_int(0, 256));
            });
        */
        // Force export of the metrics
        $GLOBALS["reader"]->collect();
        

        // Your existing response
        return response()->json(['message' => 'hello world on request ' . $count ], 200);
    }
}
