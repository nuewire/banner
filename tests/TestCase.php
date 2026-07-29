<?php

declare(strict_types=1);

namespace Nuewire\Banner\Tests;

use Livewire\LivewireServiceProvider;
use Nuewire\Banner\BannerServiceProvider;
use Nuewire\Support\SupportServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [SupportServiceProvider::class, LivewireServiceProvider::class, BannerServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));
        $app['config']->set('app.locale', 'en');
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']);
        $app['config']->set('filesystems.default', 'local');
        $app['config']->set('filesystems.disks.local', ['driver' => 'local', 'root' => sys_get_temp_dir().'/nuewire-banner-tests']);
        $app['config']->set('nuewire.banner.authorization.require_authenticated_user', false);
        $app['config']->set('nuewire.filesystem.active_disk', 'local');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate')->run();
    }
}
