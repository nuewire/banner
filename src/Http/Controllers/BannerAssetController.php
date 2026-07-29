<?php

declare(strict_types=1);

namespace Nuewire\Banner\Http\Controllers;

use Nuewire\Banner\Models\Banner;
use Nuewire\Banner\Support\BannerFileManager;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class BannerAssetController
{
    public function __invoke(Banner $banner,BannerFileManager $files):StreamedResponse{abort_unless($banner->is_aktif,404);return $files->response($banner);}
}
