<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\TrainingSchoolNotification;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendNewTrainingSchoolNotification
{

    public function handle($event)
    {
        $super_and_admin_user = (new UserRepository(new User()))->fetchAllSuperAdminAndAdminUser();
        $super_and_admin_user->notify( new TrainingSchoolNotification($event->training_school));
    }
}
