<?php

use OpenTelemetry\Contrib\Otlp\MetricExporter;
use OpenTelemetry\Sdk\Trace\TracerProvider;
use OpenTelemetry\Trace as API;

return [
    'exporter' => env('OPENTELEMETRY_EXPORTER', 'otlp'), // Use 'otlp' as the default exporter

    'exporters' => [
        'otlp' => [
            'endpoint' => env('OPENTELEMETRY_OTLP_ENDPOINT', 'http://localhost:4317'),
        ],
        // Add more exporters as needed
    ],

    'enabled' => env('OPENTELEMETRY_ENABLED', true),
];