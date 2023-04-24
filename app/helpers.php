<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\GeneralLog;

if(!function_exists('createLog')){
    function createLog($logMessage, $model = null, $targetId = null): void
    {
        GeneralLog::create([
            'auth_id' => auth()->id() ?? 1,
            'log_message' => $logMessage,
            'target' => $targetId,
            'model' => $model
        ]);
    }
}


if(!function_exists('uploadImage')){
    function uploadImage($file, $path, $candidateIndex){
        return $file->storeOnCloudinaryAs($path, $candidateIndex)->getSecurePath();
    }
}

if(!function_exists('getSuperAdmin')){
    function getSuperAdmin(){
        return User::where('user_role','super_admin')->first();
    }
}

if(!function_exists('getMonth')){
    function getMonth($date){
        $date = Carbon::parse($date);
        return $date->format('m');
    }
}

if(!function_exists('getYear')){
    function getYear($date){
        $date = Carbon::parse($date);
        return $date->format('y');
    }
}

if(!function_exists('getMonthYear')){
    function getMonthYear($date){
        $date = Carbon::parse($date);
        return $examYearMonth = $date->format('y') . $date->format('m'); // 0222
    }
}

