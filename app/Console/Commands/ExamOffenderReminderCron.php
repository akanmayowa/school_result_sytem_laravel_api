<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ExamOffenderServices;

class ExamOffenderReminderCron extends Command
{

    protected $signature = 'exam_offender_log:cron';
    protected $description = 'Command description';

    public function __construct(ExamOffenderServices $examOffenderServices){
        $this->examOffenderServices = $examOffenderServices;
        parent::__construct();
    }

    public function handle()
    {
         $this->examOffenderServices->autoReminderForFormerOffender();
    }
}
