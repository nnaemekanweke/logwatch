<?php

namespace Nnaemekanweke\Logwatch\Logging;

use Illuminate\Support\Facades\Http;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;
use Throwable;

class LogWatchHandler extends AbstractProcessingHandler
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $endpoint,
        private readonly string $logGroup,
        private readonly string $logStream,
        private readonly int $timeout = 5,
        Level $level = Level::Debug,
        bool $bubble = true,
    ) {
        parent::__construct($level, $bubble);
    }

    /**
     * Called for a single record when the handler isn't wrapped in a
     * BufferHandler (e.g. the very first record, or when buffering is
     * disabled). Sends it as a one-event batch.
     */
    protected function write(LogRecord $record): void
    {
        $this->send([$this->formatRecord($record)]);
    }

    /**
     * Called by BufferHandler with every record accumulated during the
     * request/command, flushed as a single batched API call.
     *
     * @param  array<int, LogRecord>  $records
     */
    public function handleBatch(array $records): void
    {
        if (empty($records)) {
            return;
        }

        $events = array_map($this->formatRecord(...), array_values($records));

        $this->send($events);
    }

    /**
     * @return array{message: string, timestamp: string}
     */
    private function formatRecord(LogRecord $record): array
    {
        $level = strtoupper($record->level->getName());

        return [
            'message' => "[{$level}] {$record->message}",
            'timestamp' => $record->datetime->format(\DateTimeInterface::ATOM),
        ];
    }

    /**
     * @param  array<int, array{message: string, timestamp: string}>  $events
     */
    private function send(array $events): void
    {
        try {
            Http::withToken($this->apiKey)
                ->acceptJson()
                ->timeout($this->timeout)
                ->post("{$this->endpoint}/api/v1/logs", [
                    'log_group' => $this->logGroup,
                    'log_stream' => $this->logStream,
                    'events' => $events,
                ]);
        } catch (Throwable) {
            // Never let a LogWatch delivery failure break the host application.
        }
    }
}
