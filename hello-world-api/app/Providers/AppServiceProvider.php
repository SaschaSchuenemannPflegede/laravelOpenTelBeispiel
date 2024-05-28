<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Trace\TracerProvider;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\Contrib\Grpc\GrpcTransportFactory;
use OpenTelemetry\Contrib\Otlp\OtlpUtil;
use OpenTelemetry\API\Signals;
use Illuminate\Support\Facades\Cache;
use OpenTelemetry\Contrib\Otlp\MetricExporter;
use OpenTelemetry\SDK\Metrics\MetricReader\ExportingReader;
use OpenTelemetry\SDK\Metrics\MeterProvider;
use OpenTelemetry\API\Metrics\Meter;
use OpenTelemetry\SDK\Common\Export\Http\PsrTransportFactory;
use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;

require __DIR__ . '/../../vendor/autoload.php';

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    function setupGrpcExporter(): void 
    {
        // Create an GRPC exporter based on the configuration
        $transport = (new GrpcTransportFactory())->create(env('OTEL_EXPORTER_GRPC_ENDPOINT', 'http://localhost:4317') . OtlpUtil::method(Signals::TRACE));
        $exporter = new SpanExporter($transport);

        $tracerProvider = new TracerProvider(
            new SimpleSpanProcessor($exporter)
           );

        // storing tracer object as global variable so it can be used when handling requests in controller classes
        $GLOBALS["tracer"] = $tracerProvider->getTracer('io.opentelemetry.contrib.php');

        

        $GLOBALS["reader"] = new ExportingReader(
            new MetricExporter(
                (new GrpcTransportFactory())->create(env('OTEL_EXPORTER_GRPC_ENDPOINT', 'http://localhost:4317') . OtlpUtil::method(Signals::METRICS))
            )
        );

        // Create a Meter Provider and a Meter
        $meterProvider = MeterProvider::builder()->addReader($GLOBALS["reader"])->build();
        $GLOBALS["meter"] = $meterProvider->getMeter('example-meter');
    }

    function setupHttpExporter(): void 
    {
        $transport = (new OtlpHttpTransportFactory())->create(env('OTEL_EXPORTER_OTLP_ENDPOINT', 'http://localhost:4318'), 'application/json');
        $exporter = new SpanExporter($transport);
        
        $tracerProvider = new TracerProvider(
            new SimpleSpanProcessor($exporter)
           );
           $GLOBALS["tracer"] = $tracerProvider->getTracer('io.opentelemetry.contrib.php');

           $GLOBALS["reader"] = new ExportingReader(
            new MetricExporter(
                PsrTransportFactory::discover()->create('http://localhost:4318/v1/metrics', \OpenTelemetry\Contrib\Otlp\ContentTypes::JSON)));
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Read OpenTelemetry configuration
        $config = config('opentelemetry');

        // Initialize the request_count if it doesn't exist
        if (!Cache::has('request_count')) {
            Cache::put('request_count', 0);
        }

        // Check if OpenTelemetry is enabled
        if ($config['enabled']) {
            $this->setupHttpExporter();
            
        }
    }

}
