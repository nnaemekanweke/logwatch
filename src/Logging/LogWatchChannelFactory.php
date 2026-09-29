<?php

namespace Nnaemekanweke\Logwatch\Logging;

use InvalidArgumentException;
use Monolog\Handler\BufferHandler;
use Monolog\Logger;

class LogWatchChannelFactory
{
    /**
     * Build the "logwatch" Monolog channel from config/logging.php.
     *
     * @param  array<string, mixed>  $config
     */
    public function __invoke(array $config): Logger
    {
        $apiKey = $config['api_key'] ?? config('logwatch.api_key');

        if (empty($apiKey)) {
            throw new InvalidArgumentException(
                'The "logwatch" log channel requires an api_key. Set LOGWATCH_API_KEY in your .env.'
            );
        }

        $handler = new LogWatchHandler(
            apiKey: $apiKey,
            endpoint: rtrim($config['endpoint'] ?? config('logwatch.endpoint'), '/'),
            logGroup: $config['log_group'] ?? config('logwatch.log_group'),
            logStream: $config['log_stream'] ?? config('logwatch.log_stream'),
            timeout: (int) ($config['timeout'] ?? config('logwatch.timeout', 5)),
        );

        $bufferSize = (int) ($config['buffer_size'] ?? config('logwatch.buffer_size', 50));

        return new Logger('logwatch', [
            new BufferHandler($handler, $bufferSize, flushOnOverflow: true),
        ]);
    }
}
