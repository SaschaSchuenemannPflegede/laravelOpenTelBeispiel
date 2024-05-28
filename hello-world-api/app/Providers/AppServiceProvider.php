<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use OpenTelemetry\Trace as API;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Common\Export\Http\PsrTransportFactory;
use OpenTelemetry\SDK\Common\Time\ClockFactory;
use OpenTelemetry\SDK\Metrics\MetricReader\ExportingReader;
use OpenTelemetry\SDK\Metrics\ExportMetricService;
use OpenTelemetry\SDK\Metrics\InMemoryMetricsExport;
use OpenTelemetry\SDK\Trace\TracerProvider;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\Contrib\Otlp\OtlpHttpTransportFactory;
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
            // Create an OTLP exporter based on the configuration
            //$transport = (new OtlpHttpTransportFactory())->create(env('OTEL_EXPORTER_OTLP_ENDPOINT', 'http://localhost:4317'), 'application/json');
            $transport = (new GrpcTransportFactory())->create(env('OTEL_EXPORTER_OTLP_ENDPOINT', 'http://localhost:4317') . OtlpUtil::method(Signals::TRACE));
            $exporter = new SpanExporter($transport);

            //$reader = new ExportingReader($otlpExporter);

            //$reader->collect();

            $tracerProvider = new TracerProvider(
                new SimpleSpanProcessor($exporter)
               );
            $GLOBALS["tracer"] = $tracerProvider->getTracer('io.opentelemetry.contrib.php');
            //$tracer
            //    ->spanBuilder('starting helloworld api')
            //    ->startSpan()
            //    ->end();

            //$tracer = \OpenTelemetry\API\Globals::tracerProvider()(new SimpleSpanProcessor($otlpExporter),new AlwaysOnSampler())->getTracer('Hello World Laravel Web Server');
        }
    }

}
