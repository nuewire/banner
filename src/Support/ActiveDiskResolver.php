<?php

declare(strict_types=1);

namespace Nuewire\Banner\Support;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Filesystem\FilesystemManager;
use RuntimeException;

final class ActiveDiskResolver
{
    public function __construct(private readonly Repository $config,private readonly FilesystemManager $filesystems) {}
    public function resolve(): string { $configured=trim((string)$this->config->get('nuewire.banner.storage.disk','')); $disk=$configured!==''?$configured:trim((string)$this->config->get('nuewire.filesystem.active_disk',$this->config->get('filesystems.default','local'))); if($disk===''){throw new RuntimeException('Tidak ada disk filesystem aktif untuk Nuewire Banner.');} $this->filesystems->disk($disk); return $disk; }
}
