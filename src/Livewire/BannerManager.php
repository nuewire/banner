<?php

declare(strict_types=1);

namespace Nuewire\Banner\Livewire;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Nuewire\Banner\Concerns\InteractsWithBannerManager;
use Nuewire\Banner\Models\Banner;
use Nuewire\Banner\Support\BannerActivityLogger;
use Nuewire\Banner\Support\BannerFileManager;
use Throwable;

final class BannerManager extends Component
{
    use InteractsWithBannerManager,WithFileUploads,WithPagination;
    public string $search='';public string $filter='all';public bool $editorOpen=false;public ?int $editingId=null;public string $nama='';public string $url='';public string $target='_self';public bool $isAktif=true;public TemporaryUploadedFile|string|null $upload=null;public ?string $currentAssetUrl=null;
    public bool $deleteOpen=false;public ?int $deleteId=null;public string $deleteName='';public string $deleteConfirmation='';public ?string $message=null;public ?string $errorMessage=null;
    public function mount(): void{$this->ensureAuthorized('banner.view');}
    public function updatedSearch():void{$this->resetPage();}public function updatedFilter():void{$this->resetPage();}
    public function create():void{$this->ensureAuthorized('banner.create');$this->resetEditor();$this->editorOpen=true;}
    public function edit(int $id):void{$this->ensureAuthorized('banner.update');$b=Banner::query()->findOrFail($id);$this->editingId=$b->id;$this->nama=(string)$b->nama;$this->url=(string)($b->url??'');$this->target=(string)($b->target?:'_self');$this->isAktif=(bool)$b->is_aktif;$this->currentAssetUrl=$b->assetUrl();$this->upload=null;$this->editorOpen=true;$this->resetValidation();}
    public function save(BannerFileManager $files,BannerActivityLogger $activity):void
    {
        $this->ensureAuthorized($this->editingId?'banner.update':'banner.create');$this->validate(['nama'=>['required','string','max:220'],'url'=>['nullable','string','max:2048',$this->safeUrlRule()],'target'=>['required','in:_self,_blank'],'isAktif'=>['boolean'],'upload'=>[$this->editingId?'nullable':'required','file','mimes:jpg,jpeg,png,webp,gif','max:'.max(1,(int)config('nuewire.banner.storage.max_file_size_kb',20480))]]);
        $banner=$this->editingId?Banner::query()->findOrFail($this->editingId):new Banner();$old=$banner->exists?['file_path'=>$banner->file_path,'file_disk'=>$banner->file_disk]:null;$stored=null;
        try{if($this->upload instanceof TemporaryUploadedFile){$stored=$files->store($this->upload);}$banner->fill(['nama'=>trim($this->nama),'url'=>$this->blank($this->url),'target'=>$this->target,'is_aktif'=>$this->isAktif,'user_id'=>(string)(Auth::id()??'')]);if($stored!==null){$banner->fill($stored);}$created=!$banner->exists;$banner->save();if($stored!==null&&$old&&$old['file_path']){try{$files->deleteStored($old);}catch(Throwable $e){report($e);}}$activity->record($created?'banner.created':'banner.updated',$created?'created':'updated',$banner,['nama'=>$banner->nama,'url'=>$banner->url,'target'=>$banner->target,'is_aktif'=>$banner->is_aktif,'file_extension'=>$banner->file_extension,'file_size'=>$banner->file_size]);$this->message=$created?$this->t('messages.created'):$this->t('messages.updated');$this->errorMessage=null;$this->resetEditor();}catch(Throwable $e){if($stored!==null){try{$files->deleteStored($stored);}catch(Throwable){}}report($e);$this->errorMessage=$e->getMessage();}
    }
    public function requestDelete(int $id):void{$this->ensureAuthorized('banner.delete');$b=Banner::query()->findOrFail($id);$this->deleteId=$b->id;$this->deleteName=(string)$b->nama;$this->deleteConfirmation='';$this->deleteOpen=true;}
    public function delete(BannerFileManager $files,BannerActivityLogger $activity):void{$this->ensureAuthorized('banner.delete');$b=Banner::query()->findOrFail($this->deleteId);if((bool)config('nuewire.banner.delete.require_exact_name',true)&&!hash_equals((string)$b->nama,$this->deleteConfirmation)){$this->addError('deleteConfirmation',$this->t('validation.confirm_name'));return;}$props=['nama'=>$b->nama,'file_extension'=>$b->file_extension,'file_size'=>$b->file_size];$files->delete($b);$b->delete();$activity->record('banner.deleted','deleted',$b,$props);$this->deleteOpen=false;$this->message=$this->t('messages.deleted');$this->errorMessage=null;}
    public function closeEditor():void{$this->resetEditor();}public function closeDelete():void{$this->deleteOpen=false;$this->deleteId=null;$this->deleteConfirmation='';}
    public function render()
    {
        $ready = $this->tableReady();
        $banners = null;

        if ($ready) {
            $q=Banner::query()->latest('id');
            if($this->filter==='active'){$q->where('is_aktif',true);}
            if($this->filter==='inactive'){$q->where('is_aktif',false);}
            if(trim($this->search)!==''){$q->where('nama','like','%'.trim($this->search).'%');}
            $banners = $q->paginate(15);
        }

        return view('nuewire-banner::livewire.manager',['ready'=>$ready,'banners'=>$banners,'canCreate'=>$this->canDo('banner.create'),'canUpdate'=>$this->canDo('banner.update'),'canDelete'=>$this->canDo('banner.delete')]);
    }

    private function tableReady(): bool
    {
        try {
            $model = new Banner();

            return Schema::connection($model->getConnectionName())->hasTable($model->getTable());
        } catch (Throwable) {
            return false;
        }
    }
    private function resetEditor():void{$this->editorOpen=false;$this->editingId=null;$this->nama='';$this->url='';$this->target='_self';$this->isAktif=true;$this->upload=null;$this->currentAssetUrl=null;$this->resetValidation();}
    private function blank(string $v):?string{$v=trim($v);return $v===''?null:$v;}
    private function safeUrlRule():Closure{return static function(string $attribute,mixed $value,Closure $fail):void{$value=trim((string)$value);if($value===''||str_starts_with($value,'/')||str_starts_with($value,'#')){return;}$scheme=strtolower((string)parse_url($value,PHP_URL_SCHEME));if(!in_array($scheme,['http','https','mailto','tel'],true)){$fail('URL tidak diizinkan.');}};}
    private function t(string $key,array $replace=[]):string{return trans('nuewire-banner::banner.'.$key,$replace,(string)config('nuewire.banner.locale','id'));}
}
