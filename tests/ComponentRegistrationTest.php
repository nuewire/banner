<?php

declare(strict_types=1);

namespace Nuewire\Banner\Tests;

use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

final class ComponentRegistrationTest extends TestCase
{
    public function test_component_is_registered(): void
    {
        Livewire::test('nuewire-banner')->assertStatus(200)->assertSee('Manage image banners');
    }

    public function test_component_handles_missing_table(): void
    {
        app()->setLocale('id');
        Schema::dropIfExists('nue_banner');

        Livewire::test('nuewire-banner')
            ->assertStatus(200)
            ->assertSee('Tabel Banner belum tersedia')
            ->assertSee('php artisan nuewire:banner:install --migrate')
            ->assertDontSee('Tambah Banner');
    }
}
