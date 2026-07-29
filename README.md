# Nuewire Banner

```bash
composer require nuewire/banner
php artisan nuewire:banner:install --migrate
```

Admin URL: `/admin/content/website/banner`.

Use `Nuewire\\Banner\\Models\\Banner::aktif()->latest('id')->get()` to render active banners. Each model exposes `assetUrl()`.
