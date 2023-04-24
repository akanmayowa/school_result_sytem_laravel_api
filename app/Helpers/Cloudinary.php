<?php
namespace  App\Helpers;


class Cloudinary{

    private const folder_path = 'user_profile_picture_upload';
    public static function path($path)
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    public static function uploadImage($image, $filename){
        $newFilename = str_replace(' ', '_', $filename);
        $public_id = date('Y-m-d_His').'_'.$newFilename;
        return cloudinary()->upload($image, [
            "public_id" => self::path($public_id),
            "folder"    => self::folder_path
        ])->getSecurePath();
    }

    public static function replace($path, $image, $public_id)
    {
        self::delete($path);
        return self::uploadImage($image, $public_id);
    }

    public static function delete($path)
    {
        $public_id = self::folder_path.'/'.self::path($path);
        return cloudinary()->destroy($public_id);
    }

}
