# Panduan Nuewire Banner

Package mengelola banner gambar, URL tujuan, target tab, status aktif, dan metadata storage.

```bash
php artisan nuewire:install --feature=banner
php artisan migrate
```

Disk mengikuti `NUEWIRE_BANNER_DISK`, active disk Nuewire Filesystem, lalu default Laravel. SVG tidak diizinkan. Penghapusan permanen memerlukan nama Banner persis karena skema tidak memiliki Archive.

Frontend:

```php
$banners = Nuewire\\Banner\\Models\\Banner::aktif()->latest('id')->get();
```

Gunakan `$banner->assetUrl()` untuk gambar. Jika target `_blank`, tambahkan `rel="noopener noreferrer"`.
