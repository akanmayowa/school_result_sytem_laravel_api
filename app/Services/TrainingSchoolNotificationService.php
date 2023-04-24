<?php

namespace App\Services;

use App\Traits\ResponsesTrait;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class TrainingSchoolNotificationService
{

    use ResponsesTrait;

    public function fetchAllUnReadNotification()
    {
        if((Gate::allows('isSuperAdmin')) || (Gate::allows('isAdmin')) ) {

            $notifications = auth()->user()->unreadNotifications;
            return $this->successResponse($notifications,"UnRead Notification successfully fetched!");
        }else{
            dd('Only Super Admin and Admin are allowed');
        }
    }

    public function fetchAllNotification(): \Illuminate\Http\JsonResponse
    {
        if((Gate::allows('isSuperAdmin')) || (Gate::allows('isAdmin')) ) {
            $notifications = DB::table('notifications')
                ->latest()->where('action_type', 'candidate_indexed')->paginate(10);
            return $this->successResponse($notifications,"UnRead Notification successfully fetched!");
        }
        return response()->json('Only Super Admin and Admin are allowed');
    }


    public function fetchAllReadNotification()
    {
        if((Gate::allows('isSuperAdmin')) || (Gate::allows('isAdmin')) )
        {
            $notifications =  DB::table('notifications')->where('action_type', 'candidate_indexed')->latest()->get();
            return $this->successResponse($notifications,"Read Notification successfully fetched!");
        }else{
            Response::denyWithStatus(404);
            dd('Only Super Admin and Admin are allowed');
        }
    }

    public function markNotificationAsRead()
    {
        if((Gate::allows('isSuperAdmin')) || (Gate::allows('isAdmin')) ) {
            $validator = Validator::make(request()->all(), ['id' => 'required',]);
            if($validator->fails()) { return $this->errorResponse($validator->errors());}
            $id = request()->input('id');
             DB::table('notifications')->where('id',$id)->update(['read_at' => Carbon::now()]);
                return $this->successResponse("Notification Status Changed operation successful");
        }else{
                return  Response::denyWithStatus(404,'You must be an a super administrator or administrator.');
        }
    }

    public function countNotifications()
    {
        $count_unread_notifications = auth()->user()->unreadNotifications->count();
        return response()->json(['data' => $count_unread_notifications], 202);
    }

}
