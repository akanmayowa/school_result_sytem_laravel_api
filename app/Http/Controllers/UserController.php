<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserProfileRequest;
use App\Services\UserProfileService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public ? UserProfileService $user_profile_services = null;
    public function  __construct(UserProfileService $user_profile_services)
    {
        $this->user_profile_services = $user_profile_services;
    }

    public function update()
    {
        return $this->user_profile_services->userProfileUpdate();
    }
    public function updateProfileImage()
    {
        return $this->user_profile_services->userProfileImage();
    }

    public function updateUserTrainingSchoolRelationship()
    {
        return $this->user_profile_services->userProfileUpdateForTrainingSchool();
    }

    public function updateUserTrainingSchoolRelationshipImage()
    {
        return $this->user_profile_services->userProfileImageForTrainingSchool();
    }

    public function authenticatedUserInformation()
    {
       return $this->user_profile_services->fetchAuthenticatedUser();
    }

}
