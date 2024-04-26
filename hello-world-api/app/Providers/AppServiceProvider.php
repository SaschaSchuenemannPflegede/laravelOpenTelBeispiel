<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use OpenTelemetry\Trace as API;
use OpenTelemetry\Exporter\OTLPExporter;
use OpenTelemetry\Contrib\Otlp\MetricExporter;
use OpenTelemetry\SDK\Common\Export\Http\PsrTransportFactory;
use OpenTelemetry\SDK\Common\Time\ClockFactory;
use OpenTelemetry\SDK\Metrics\MetricReader\ExportingReader;
use OpenTelemetry\Sdk\Metrics\ExportMetricService;
use OpenTelemetry\Sdk\Metrics\InMemoryMetricsExport;
use OpenTelemetry\Sdk\Trace\TracerProvider;

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
            $otlpExporter = new MetricExporter(
                PsrTransportFactory::discover()->create('http://collector:4318/v1/metrics', \OpenTelemetry\Contrib\Otlp\ContentTypes::JSON)
            );

            $reader = new ExportingReader($otlpExporter);

            $reader->collect();
        }
    }

}
