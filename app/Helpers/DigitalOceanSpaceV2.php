<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class DigitalOceanSpaceV2
{
    public static function path($path)
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

//    public static function uploadImage($folder_path, $image, $filename){
//        $newFilename = str_replace(' ', '_', $filename);
//        $public_id = date('Y-m-d_His').'_'.$newFilename;
//        return  Storage::disk('digitalocean')->putFileAs(  `${folder_path}/`, $image, $public_id, 'public');
//    }

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



    public static function replace($folder_path, $path, $image, $public_id)
    {
        self::delete($folder_path, $path);
        return self::uploadImage($folder_path,$image, $public_id);
    }

    public static function delete($folder_path,$path)
    {
        $public_id = $folder_path.'/'.self::path($path);
        Storage::disk('digitalocean')->delete($public_id);
    }


}
