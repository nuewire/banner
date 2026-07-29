<?php

declare(strict_types=1);

namespace Nuewire\Banner\Concerns;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

trait InteractsWithBannerManager
{
    private function ensureAuthorized(string $permission): void { $cfg=(array)config('nuewire.banner.authorization',[]);$guard=trim((string)($cfg['guard']??''));$auth=$guard===''?Auth::guard():Auth::guard($guard);if((bool)($cfg['require_authenticated_user']??true)&&!$auth->check()){abort(403);}$user=$auth->user();if(app()->bound('nuewire.acl.enabled')){if($user===null||!method_exists($user,'can')){abort(403);}try{abort_unless($user->can($permission),403);}catch(Throwable){abort(403);}}$gate=trim((string)($cfg['gate']??''));if($gate!==''&&($user===null||Gate::forUser($user)->denies($gate))){abort(403);} }
    private function canDo(string $permission): bool { try{$this->ensureAuthorized($permission);return true;}catch(HttpException){return false;} }
}
