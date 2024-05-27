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

        // Check if OpenTelemetry is enabled
        if ($config['enabled']) {
            // Create an OTLP exporter based on the configuration
            $transport = (new OtlpHttpTransportFactory())->create(env('OTEL_EXPORTER_OTLP_ENDPOINT', 'http://localhost:4317'), 'application/json');
            $exporter = new SpanExporter($transport);

            //$reader = new ExportingReader($otlpExporter);

            //$reader->collect();

            $tracerProvider = new TracerProvider(
                new SimpleSpanProcessor($exporter)
               );
            $tracer = $tracerProvider->getTracer('io.opentelemetry.contrib.php');

            //$tracer = \OpenTelemetry\API\Globals::tracerProvider()(new SimpleSpanProcessor($otlpExporter),new AlwaysOnSampler())->getTracer('Hello World Laravel Web Server');
        }
    }

}
