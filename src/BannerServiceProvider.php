<?php

declare(strict_types=1);

namespace Nuewire\Banner;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Nuewire\Banner\Commands\InstallCommand;
use Nuewire\Banner\Livewire\BannerManager;
use Nuewire\Banner\Models\Banner;
use Nuewire\Banner\Support\ActiveDiskResolver;
use Nuewire\Banner\Support\BannerActivityLogger;
use Nuewire\Banner\Support\BannerFileManager;
use Nuewire\Support\LivewireComponentRegistrar;
use Nuewire\Support\NuewirePaths;

final class BannerServiceProvider extends ServiceProvider
{
    public function register():void{$this->replaceConfigRecursivelyFrom(__DIR__.'/../config/nuewire/banner.php','nuewire.banner');$this->app->singleton(ActiveDiskResolver::class);$this->app->singleton(BannerFileManager::class);$this->app->singleton(BannerActivityLogger::class);if($this->app->runningInConsole()){$this->commands([InstallCommand::class]);}$this->registerNavigation();$this->registerDashboard();$this->registerPermissions();}
    public function boot():void{$paths=$this->app->make(NuewirePaths::class);$this->loadViewsFrom(__DIR__.'/../resources/views','nuewire-banner');$this->loadTranslationsFrom(__DIR__.'/../resources/lang','nuewire-banner');$this->loadMigrationsFrom(__DIR__.'/../database/migrations');if((bool)config('nuewire.banner.public_asset_route.enabled',true)){$this->loadRoutesFrom(__DIR__.'/../routes/web.php');}$this->app->make(LivewireComponentRegistrar::class)->register('nuewire-banner',BannerManager::class);$this->publishes([__DIR__.'/../config/nuewire/banner.php'=>$paths->configFile('banner')],'nuewire-banner-config');$this->publishes([__DIR__.'/../resources/views'=>$paths->publishedViews('banner')],'nuewire-banner-views');$this->publishes([__DIR__.'/../resources/lang'=>$paths->publishedTranslations('banner')],'nuewire-banner-translations');}
    private function registerNavigation():void{$class='Nuewire\\Platform\\Navigation\\NavigationRegistry';$this->app->afterResolving($class,static function(object $registry):void{if(!method_exists($registry,'register')){return;}if(!method_exists($registry,'registerArea')){$registry->register('banner',['label'=>['id'=>'Banner','en'=>'Banner'],'group'=>['id'=>'Konten','en'=>'Content'],'component'=>'nuewire-banner','permission'=>'banner.view','order'=>23]);return;}if(method_exists($registry,'registerGroup')){$registry->registerGroup('content','website',['label'=>['id'=>'Website','en'=>'Website'],'order'=>10]);}$nested=method_exists($registry,'supportsNestedPaths')&&$registry->supportsNestedPaths();$registry->register('banner.index',['area'=>'content','group'=>'website','slug'=>$nested?'website/banner':'banner','aliases'=>$nested?['banner']:[],'label'=>['id'=>'Banner','en'=>'Banner'],'description'=>['id'=>'Kelola banner gambar dan tautan website.','en'=>'Manage website image banners and links.'],'component'=>'nuewire-banner','permission'=>'banner.view','icon'=>'content','order'=>30]);});}
    private function registerDashboard():void{$class='Nuewire\\Platform\\Dashboard\\DashboardRegistry';$this->app->afterResolving($class,static function(object $registry):void{if(!method_exists($registry,'register')){return;}if(method_exists($registry,'registerGroup')){$registry->registerGroup('content',['label'=>['id'=>'Konten','en'=>'Content'],'order'=>30]);}$ready=static fn():bool=>Schema::hasTable((string)config('nuewire.banner.table','nue_banner'));$registry->register('banner.active-total',['group'=>'content','label'=>['id'=>'Banner Aktif','en'=>'Active Banners'],'description'=>['id'=>'Jumlah banner yang sedang aktif.','en'=>'Number of active banners.'],'type'=>'stat','permission'=>'banner.view','visible'=>$ready,'width'=>3,'default'=>false,'cache_ttl'=>120,'cache_scope'=>'global','resolver'=>static fn(object $context):array=>['value'=>number_format(Banner::query()->aktif()->count()),'meta'=>$context->locale==='en'?'Published image banners':'Banner gambar aktif','url'=>$context->route('content','banner')],'order'=>44]);});}
    private function registerPermissions():void{$class='Nuewire\\Acl\\Contracts\\PermissionRegistry';$this->app->afterResolving($class,static function(object $registry):void{if(method_exists($registry,'registerMany')){$registry->registerMany(['banner.view'=>['id'=>'Melihat Banner','en'=>'View Banner'],'banner.create'=>['id'=>'Membuat Banner','en'=>'Create Banner'],'banner.update'=>['id'=>'Mengubah Banner','en'=>'Update Banner'],'banner.delete'=>['id'=>'Menghapus Banner dan file','en'=>'Delete Banner and file']],'banner');}});}
}
