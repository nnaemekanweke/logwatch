<?php

namespace Nnaemekanweke\Logwatch\Tests;

use Nnaemekanweke\Logwatch\LogWatchServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [LogWatchServiceProvider::class];
    }
}
