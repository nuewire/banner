<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $name=(string)config('nuewire.banner.table','nue_banner'); if (Schema::hasTable($name)) { return; }
        Schema::create($name,static function(Blueprint $table): void {
            $table->id(); $table->string('nama',220); $table->string('url',2048)->nullable(); $table->string('target',20)->default('_self');
            $table->string('file_path',1024); $table->string('file_disk',100); $table->string('file_extension',20); $table->unsignedBigInteger('file_size')->default(0);
            $table->boolean('is_aktif')->default(true)->index(); $table->string('user_id',64)->nullable()->index();
        });
    }
    public function down(): void { Schema::dropIfExists((string)config('nuewire.banner.table','nue_banner')); }
};
