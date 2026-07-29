<?php

declare(strict_types=1);

return [
    'table' => env('NUEWIRE_BANNER_TABLE', 'nue_banner'),
    'locale' => env('NUEWIRE_BANNER_LOCALE', 'id'),
    'supported_locales' => ['id','en'],
    'storage' => [
        'disk' => env('NUEWIRE_BANNER_DISK'),
        'directory' => env('NUEWIRE_BANNER_DIRECTORY', 'banner'),
        'max_file_size_kb' => (int) env('NUEWIRE_BANNER_MAX_FILE_SIZE_KB', 20480),
        'allowed_extensions' => ['jpg','jpeg','png','webp','gif'],
    ],
    'public_asset_route' => [
        'enabled' => (bool) env('NUEWIRE_BANNER_PUBLIC_ASSET_ROUTE', true),
        'prefix' => env('NUEWIRE_BANNER_PUBLIC_ASSET_PREFIX', 'nuewire/banner-assets'),
        'middleware' => ['web'],
    ],
    'authorization' => ['require_authenticated_user'=>true,'guard'=>env('NUEWIRE_BANNER_GUARD'),'gate'=>env('NUEWIRE_BANNER_GATE')],
    'delete' => ['require_exact_name'=>true],
];
