<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Nuewire\Banner\Http\Controllers\BannerAssetController;

$prefix=trim((string)config('nuewire.banner.public_asset_route.prefix','nuewire/banner-assets'),'/');
Route::middleware((array)config('nuewire.banner.public_asset_route.middleware',['web']))->get($prefix.'/{banner}',BannerAssetController::class)->whereNumber('banner')->name('nuewire.banner.asset');
