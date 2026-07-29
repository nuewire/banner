<?php

declare(strict_types=1);

namespace Nuewire\Banner\Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

final class SchemaTest extends TestCase{public function test_schema_matches_contract():void{Artisan::call('migrate',['--force'=>true]);$this->assertTrue(Schema::hasColumns('nue_banner',['id','nama','url','target','file_path','file_disk','file_extension','file_size','is_aktif','user_id']));}}
