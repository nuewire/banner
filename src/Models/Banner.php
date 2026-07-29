<?php

declare(strict_types=1);

namespace Nuewire\Banner\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class Banner extends Model
{
    public $timestamps=false; protected $guarded=[]; protected $casts=['is_aktif'=>'boolean','file_size'=>'integer'];
    public function __construct(array $attributes=[]) { parent::__construct($attributes); $this->setTable((string)config('nuewire.banner.table','nue_banner')); }
    public function scopeAktif(Builder $query): Builder { return $query->where('is_aktif',true); }
    public function assetUrl(): ?string { if (! $this->file_path || ! (bool)config('nuewire.banner.public_asset_route.enabled',true)) { return null; } return route('nuewire.banner.asset',['banner'=>$this->getKey()]); }
}
