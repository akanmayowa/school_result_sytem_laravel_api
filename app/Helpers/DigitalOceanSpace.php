<?php

namespace App\Helpers;


use Illuminate\Support\Facades\Storage;

class DigitalOceanSpace
{

    private const folder_path = 'user_profile_picture_upload';
    public static function path($path)
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    public static function uploadImage($folder_path, $image, $filename = null){
        $filename = $filename ?? $image->getClientOriginalName();

        $newFilename = str_replace(' ', '_', $filename);
        $public_id = date('Y-m-d_His').'_'.$newFilename;

        $uploaded = Storage::disk('digitalocean')->put("$folder_path/$public_id", file_get_contents($image), 'public');

        if ($uploaded) {
            return config('filesystems.disks.digitalocean.url') . '/' . $folder_path . '/' . $public_id;
        }

        return null;
    }


    public static function replace($path, $image, $public_id)
    {
        self::delete($path);
        return self::uploadImage($image, $public_id);
    }

    public static function delete($path)
    {
        $public_id = self::folder_path.'/'.self::path($path);
        Storage::disk('digitalocean')->delete($public_id);
    }

    public function checkIfImageeExsist()
    {
        if (Storage::disk('s3')->exists('file.jpg')) return response()->json("file Exsist Hurray!");
        return true;
    }

    public function fetchImage()
    {
       Storage::disk('spaces')->url('file-path and name');
    }


//    public function updateImage()
//    {
//        $fileName = $request->validated()['doctorProfileImageFileName'];
//        $folder = config('filesystems.php.disks.do.folder');
//        Storage::disk('do')->put("{$folder}/{$fileName}", file_get_contents($file));
//        $this->cdnService->purge($fileName);
//    }

}
