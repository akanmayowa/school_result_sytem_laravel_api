<?php /** @noinspection ALL */

namespace App\Helpers;

use Carbon\Carbon;

class PrintDocument
{
           public static function getMonth($date)
            {
                $date = Carbon::parse($date);
                return $date->format('m');
            }

            public static function getYear($date)
            {
                $date = Carbon::parse($date);
                return $date->format('y');
            }


            public static function getMonthYear($date)
            {
                $date = Carbon::parse($date);
                return $examYearMonth = $date->format('y') . $date->format('m'); // 0222
            }
}
