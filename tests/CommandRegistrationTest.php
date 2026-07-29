<?php

declare(strict_types=1);

namespace Nuewire\Banner\Tests;

final class CommandRegistrationTest extends TestCase{public function test_command_is_registered():void{$this->assertArrayHasKey('nuewire:banner:install',app('artisan')->all());}}
