<?php

namespace App\Http\Controllers;

use App\Services\TrainingSchoolNotificationService;
use Illuminate\Http\Request;

class TrainingSchoolNotificationController extends Controller
{
    public  $training_school_notification_services = null;
    public function __construct(TrainingSchoolNotificationService $training_school_notification_services)
    {
        $this->training_school_notification_services = $training_school_notification_services;
    }

    public function getAllUnReadNotification()
    {
        return $this->training_school_notification_services->fetchAllUnReadNotification();
    }

    public function changeNotificationStatus()
    {
        return $this->training_school_notification_services->markNotificationAsRead();
    }


    public function getAllReadNotification()
    {
        return $this->training_school_notification_services->fetchAllReadNotification();
    }

    public function fetchAllNotification(): \Illuminate\Http\JsonResponse
    {
        return $this->training_school_notification_services->fetchAllNotification();
    }


    public function countUnreadNotifications(): \Illuminate\Http\JsonResponse
    {
        return $this->training_school_notification_services->countNotifications();
    }
}
