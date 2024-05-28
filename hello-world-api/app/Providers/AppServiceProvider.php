<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Trace\TracerProvider;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\Contrib\Grpc\GrpcTransportFactory;
use OpenTelemetry\Contrib\Otlp\OtlpUtil;
use OpenTelemetry\API\Signals;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Read OpenTelemetry configuration
        $config = config('opentelemetry');

        // iniate request counter
        $GLOBALS["request_count"] = 0;

        // Check if OpenTelemetry is enabled
        if ($config['enabled']) {
            // Create an GRPC exporter based on the configuration
            $transport = (new GrpcTransportFactory())->create(env('OTEL_EXPORTER_GRPC_ENDPOINT', 'http://localhost:4317') . OtlpUtil::method(Signals::TRACE));
            $exporter = new SpanExporter($transport);

            $tracerProvider = new TracerProvider(
                new SimpleSpanProcessor($exporter)
               );

            // storing tracer object as global variable so it can be used when handling requests in controller classes
            $GLOBALS["tracer"] = $tracerProvider->getTracer('io.opentelemetry.contrib.php');
            
        }
    }

}
