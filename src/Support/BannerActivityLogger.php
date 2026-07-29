<?php

declare(strict_types=1);

namespace Nuewire\Banner\Support;

use Illuminate\Database\Eloquent\Model;
use Throwable;

final class BannerActivityLogger
{
    /** @param array<string,mixed> $properties */
    public function record(string $description,string $event,Model $subject,array $properties=[]): void { $class='Nuewire\\Logs\\Support\\AuditLogger';if(!class_exists($class)||!app()->bound($class)){return;}try{app($class)->record($description,$subject,$properties,auth()->user(),$event,'content');}catch(Throwable $e){report($e);} }
}
