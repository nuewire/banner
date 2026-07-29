<?php

declare(strict_types=1);

namespace Nuewire\Banner\Support;

use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Nuewire\Banner\Models\Banner;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class BannerFileManager
{
    public function __construct(private readonly FilesystemManager $filesystems,private readonly ActiveDiskResolver $disks) {}
    /** @return array{file_path:string,file_disk:string,file_extension:string,file_size:int} */
    public function store(UploadedFile $file): array
    {
        $extension=strtolower((string)($file->extension()?:$file->getClientOriginalExtension())); $extension=preg_replace('/[^a-z0-9]+/','',$extension)?:'bin';
        $allowed=array_values(array_filter((array)config('nuewire.banner.storage.allowed_extensions',[]),'is_string')); if($allowed!==[]&&!in_array($extension,$allowed,true)){throw new RuntimeException('Ekstensi file Banner tidak diizinkan.');}
        $max=max(1,(int)config('nuewire.banner.storage.max_file_size_kb',20480))*1024; if((int)$file->getSize()>$max){throw new RuntimeException('Ukuran file Banner melebihi batas konfigurasi.');}
        $disk=$this->disks->resolve(); $path=$this->directory().'/'.now()->format('Y/m'); $stored=$this->filesystems->disk($disk)->putFileAs($path,$file,Str::uuid()->toString().'.'.$extension);
        if($stored===false||$stored===''){throw new RuntimeException('File Banner gagal disimpan.');}
        return ['file_path'=>$this->managedPath((string)$stored),'file_disk'=>$disk,'file_extension'=>$extension,'file_size'=>max(0,(int)$file->getSize())];
    }
    public function exists(Banner $banner): bool { try{return $banner->file_path&&$banner->file_disk&&$this->filesystems->disk((string)$banner->file_disk)->exists($this->managedPath((string)$banner->file_path));}catch(RuntimeException){return false;} }
    public function delete(Banner $banner): void { if(!$banner->file_path||!$banner->file_disk){return;} $disk=$this->filesystems->disk((string)$banner->file_disk);$path=$this->managedPath((string)$banner->file_path);if($disk->exists($path)&&!$disk->delete($path)){throw new RuntimeException('File Banner gagal dihapus.');} }
    /** @param array{file_path?:mixed,file_disk?:mixed} $stored */ public function deleteStored(array $stored): void { $disk=trim((string)($stored['file_disk']??''));$path=trim((string)($stored['file_path']??''));if($disk!==''&&$path!==''){$this->filesystems->disk($disk)->delete($this->managedPath($path));} }
    public function response(Banner $banner): StreamedResponse { if(!$this->exists($banner)){throw new RuntimeException('File Banner tidak ditemukan.');} return $this->filesystems->disk((string)$banner->file_disk)->response($this->managedPath((string)$banner->file_path),basename((string)$banner->file_path),['Content-Disposition'=>'inline']); }
    private function directory(): string { $v=trim(str_replace('\\','/',(string)config('nuewire.banner.storage.directory','banner')),'/');if($v===''||$this->unsafe($v)){throw new RuntimeException('Direktori Banner tidak aman.');}return $v; }
    private function managedPath(string $path): string { $path=trim(str_replace('\\','/',$path),'/');if($path===''||$this->unsafe($path)){throw new RuntimeException('Path Banner tidak valid.');}$d=$this->directory();if($path!==$d&&!str_starts_with($path,$d.'/')){throw new RuntimeException('Path berada di luar direktori Banner.');}return $path; }
    private function unsafe(string $path): bool { foreach(explode('/',$path)as$s){if($s===''||$s==='.'||$s==='..'){return true;}}return false; }
}
