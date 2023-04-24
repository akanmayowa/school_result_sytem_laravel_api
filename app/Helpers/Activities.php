<?php

namespace App\Helpers;

use App\Models\ScoreLog;
use App\Models\Activity;

class Activities{

  public static function activityLog($activity, $data_table, $user_agent, $data_unique_id): void
    {
        Activity::create([
            'activity' => $activity,
            'data_table' => $data_table,
            'user_agent' => $user_agent,
            'data_unique_id' => $data_unique_id
        ]);
    }

    public static function scoreLog($scores, $candidate_index, $admin_id, $course_key, $course_header, $type, $exam_date): void
    {
        ScoreLog::create([
            'q1' => $scores['q1'],
            'q2' => $scores['q2'],
            'q3' => $scores['q3'],
            'q4' => $scores['q4'],
            'q5' => $scores['q5'],
            'candidate_index' => $candidate_index,
            'admin_id' => $admin_id,
            'course_header' => $course_header,
            'course_key' => $course_key,
            'marker_key' => $type,
            'exam_date' => $exam_date
        ]);
    }
    public static function uploadImage($file, $path, $candidateIndex)
    {
        return $file->storeOnCloudinaryAs($path, $candidateIndex)->getSecurePath();
    }
}
