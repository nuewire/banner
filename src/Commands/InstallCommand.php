<?php

declare(strict_types=1);

namespace Nuewire\Banner\Commands;

use Illuminate\Console\Command;

final class InstallCommand extends Command
{
    protected $signature='nuewire:banner:install {--migrate : Run pending migrations}';protected $description='Publish Nuewire Banner configuration and optionally run migrations.';
    public function handle():int{$this->call('vendor:publish',['--tag'=>'nuewire-banner-config','--force'=>true]);if($this->option('migrate')){$this->call('migrate',['--force'=>true]);}$this->components->info('Nuewire Banner is ready.');return self::SUCCESS;}
}
