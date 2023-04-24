<?php

namespace App\Helpers;

use App\Models\GeneralLog;
use Illuminate\Support\Facades\Auth;

class GeneralLogs
{

    public static function createLog($log_message, $target_id = null)
    {
            GeneralLog::create([
                'auth_id' => auth()->id(),
            'log_message' => $log_message,
            'target' => $target_id,
        ]);
    }

    public static function uploadImage($file, $path, $candidateIndex){
        return $file->storeOnCloudinaryAs($path, $candidateIndex)->getSecurePath();
    }

}
