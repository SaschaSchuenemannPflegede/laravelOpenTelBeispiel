# Laravel Beispiel für Open Telemetry Monitoring

Hier handelt es sich um eine simple Laravel API die mittels [opentelementry-php](https://github.com/open-telemetry/opentelemetry-php) Monitoring Daten (bisher span und metriken) an einen OTLP Collector schickt wenn immer ein Aufruf auf den Endpunkt `/hello-world` stattfindet.

In der Datei `/app/Providers/AppServiceProvider.php` wird dazu ein OTLP-Exporter konfiguriert (wahlweise via GRPC oder HTTP+JSON) und dann als globale Variablen ein Tracer (für das Senden von Spans als Teil von Traces) und ein Reader (für das Senden von Metriken) bereitgestellt.


Für das Testen werden im Controller des `hello-world` Endpunkts in der Datei `/app/Http/Controllers/HelloWorldController.php` die Aufrufe gezählt und dieser Counter sowohl als Metrik als auch als Event eines Spans an den Collector geschickt.

Die Konfiguration des Endpunkts findet über die  `.env` Datei statt.

## Testen

Wie üblich kann die Laravel API mit `php artisan serve` lokal getestet werden.
Per default werden Metriken und Traces an `localhost:4318` (wenn der http-exporter genutzt wird) bzw. `localhost:4317` (für den GRPC-exporter) geschickt.

Lokal kann dazu ein collector mittels podman/docker gestartet werden:
`podman run -p 4317:4317 -p 4318:4318 -p 55679:55679 otel/opentelemetry-collector-contrib`

Da der collector sich selbst regelmäßig Metriken schickt empfiehlt es sich den Output in ein Logfile zu pipen um dort effizient nach den Metriken suchen zu können:
`podman run -p 4317:4317 -p 4318:4318 -p 55679:55679 otel/opentelemetry-collector-contrib > collector.log 2>&1`

Dann könnte man in der Logdatei zum Beispiel nach den Namen der Metriken oder des Events suchen.

