<?php

declare(strict_types=1);

namespace HosseinAskari\LaravelBale;

use HosseinAskari\LaravelBale\Application\BaleManager;
use HosseinAskari\LaravelBale\Contracts\BaleClientInterface;
use HosseinAskari\LaravelBale\Contracts\BaleMessageSenderInterface;
use HosseinAskari\LaravelBale\Infrastructure\Http\BaleHttpClient;
use Illuminate\Contracts\Container\Container;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class BaleServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('bale')
            ->hasConfigFile('bale');
    }

    public function registeringPackage(): void
    {
        $this->app->singleton(BaleClientInterface::class, function (Container $app): BaleClientInterface {
            return new BaleHttpClient(
                config: $app['config']->get('bale', []),
                loggerFactory: static fn (?string $channel) => $channel ? $app['log']->channel($channel) : $app['log'],
            );
        });

        $this->app->singleton(BaleManager::class, function (Container $app): BaleManager {
            return new BaleManager(
                client: $app->make(BaleClientInterface::class),
                defaultBotId: $app['config']->get('bale.default_bot_id'),
            );
        });

        $this->app->alias(BaleManager::class, BaleMessageSenderInterface::class);
        $this->app->alias(BaleManager::class, 'bale');
    }
}
