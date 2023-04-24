<?php

namespace App\Http\Controllers;

use App\Services\BroadCastMessageServices;


class BroadCastMessageController extends Controller
{
    public ? BroadCastMessageServices $broad_cast_message_services = null;

    public function __construct(BroadCastMessageServices $broad_cast_message_services)
    {
        $this->broad_cast_message_services = $broad_cast_message_services;
    }

    public function sentMessageToAllTrainingSchool()
    {
        return $this->broad_cast_message_services->sentMessageToAllTrainingSchool();
    }

    public function sendBroadCastToSelectedTrainingSchool()
    {
        return $this->broad_cast_message_services->sendMessageToSelectedTrainingSchool();
    }

    public function sendBroadCastToTrainingSchoolBasedOnSelectedCourses()
    {
        return $this->broad_cast_message_services->sendMessageToTrainingSchoolBasedOnSelectedCourses();
    }

    public function sendBroadCastToWahebAdmin()
    {
        return $this->broad_cast_message_services->sendMessageToWahebAdmin();
    }

    public function fetchAllBroadCastForAdmins()
    {
        return $this->broad_cast_message_services->fetchAllBroadCastForAdmin();
    }

    public function fetchAllBroadCastForSchoolAdmin()
    {
        return $this->broad_cast_message_services->fetchAllBroadCastForSchoolAdmin();
    }

    public function changeBroadCastMessageStatus()
    {
         return $this->broad_cast_message_services->changeBroadCastMessageReadOrUnread();
    }

    public function show()
    {
        return $this->broad_cast_message_services->getSingleMessage();
    }

    public function show_II()
    {
        return $this->broad_cast_message_services->getSingleNotification();
    }
}
