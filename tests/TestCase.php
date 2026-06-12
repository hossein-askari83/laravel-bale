<?php

declare(strict_types=1);

namespace HosseinAskari\LaravelBale\Tests;

use HosseinAskari\LaravelBale\BaleServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [BaleServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('bale.token', 'test-token');
        $app['config']->set('bale.base_url', 'https://safir.bale.ai/api/v3');
        $app['config']->set('bale.default_bot_id', 123456789);
        $app['config']->set('bale.logging.enabled', false);
    }
}
