<?php

declare(strict_types=1);

namespace Nuewire\Banner\Tests;

use Livewire\Livewire;

final class ComponentRegistrationTest extends TestCase
{
    public function test_component_is_registered(): void
    {
        Livewire::test('nuewire-banner')->assertStatus(200)->assertSee('Manage image banners');
    }
}
