<?php
namespace App\Helpers;

Class ResultAnalysis{
public static function CheckIfItemExistInAnArray($array, $key)
{
    $result = array();
    $i = 0;
    $result_key = array();
    foreach ($array as $val) {
        if (!in_array($val[$key], $result_key)) {
            $result_key[$i] = $val[$key];
            $result[$i] = $val;
        }
        $i++;
    }
    return $result;
}
}
