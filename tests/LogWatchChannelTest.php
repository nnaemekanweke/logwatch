<?php

namespace Nnaemekanweke\Logwatch\Tests;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Nnaemekanweke\Logwatch\Logging\LogWatchChannelFactory;

class LogWatchChannelTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        $app['config']->set('logging.channels.logwatch', [
            'driver' => 'custom',
            'via' => LogWatchChannelFactory::class,
            'api_key' => 'test-key',
            'endpoint' => 'https://logwatch.test',
            'log_group' => 'my-app',
            'log_stream' => 'production',
        ]);
    }

    public function test_it_merges_default_config(): void
    {
        $this->assertSame('http://localhost:8000', config('logwatch.endpoint'));
        $this->assertSame(50, config('logwatch.buffer_size'));
    }

    public function test_it_posts_a_batched_payload_to_the_ingestion_endpoint(): void
    {
        Http::fake([
            'logwatch.test/api/v1/logs' => Http::response(['events_ingested' => 1], 201),
        ]);

        Log::channel('logwatch')->info('request completed');

        // BufferHandler flushes on PHP shutdown; force a flush now via the channel itself.
        app('log')->channel('logwatch')->getHandlers()[0]->close();

        Http::assertSent(function ($request) {
            return $request->url() === 'https://logwatch.test/api/v1/logs'
                && $request->hasHeader('Authorization', 'Bearer test-key')
                && $request['log_group'] === 'my-app'
                && $request['log_stream'] === 'production'
                && count($request['events']) === 1
                && str_contains($request['events'][0]['message'], 'request completed');
        });
    }

    public function test_it_requires_an_api_key(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new LogWatchChannelFactory)([
            'endpoint' => 'https://logwatch.test',
            'log_group' => 'my-app',
            'log_stream' => 'production',
        ]);
    }
}
