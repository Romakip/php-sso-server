<?php

namespace App\Services;

use Basis\Nats\Client;
use Basis\Nats\Configuration;
use Illuminate\Support\Facades\Log;
use App\Contracts\EventDispatcherInterface;

class NatsPublisherService implements EventDispatcherInterface
{
    public function __construct(protected Client $client)
    {
        $config = new Configuration([
            'host' => env('NATS_HOST',),
            'port' => (int) env('NATS_PORT', 4222),
            'nkey' => env('NATS_NKEY_SEED'),
            'reconnect' => true,
            'timeout' => 1,
            'verbose' => true,
        ]);

        $this->client = new Client($config);
    }

    public function dispatch(string $stream, string $subject, string $event, array $payload): void
    {
        try {
            $messageToStream = serialize([
                'headers' => ['event' => $event],
                'body' => json_encode($payload),
            ]);

            $stream = $this->client->getApi()->getStream($stream);
            $stream->publish($subject, $messageToStream);

        } catch (\Throwable $e) {
            Log::warning("[NATS] Failed to publish to {$subject}: " . $e->getMessage());
        }
    }
}
