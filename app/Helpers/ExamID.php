<?php

namespace App\Helpers;

use App\Models\CourseHeader;

class ExamID
{
    public function getExamMonth($course_header): string
    {
        $courseHeader = CourseHeader::whereHeaderKey($course_header)->first();

        return $courseHeader->month ?? '10';
    }

    public function getExamId($course_header)
    {
        $month = $this->getExamMonth($course_header);

        $year = str_split(now()->format('Y'), 2)[1];

        return $month . $year;
    }
}
