<?php /** @noinspection MethodShouldBeFinalInspection */

namespace App\Repositories;

use App\Models\PasswordReset;
use App\Models\User;
use App\Notifications\ForgetPasswordNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserRepository extends BaseRepository
{
    public $model;

    public function __construct(User $model)
    {
        parent::__construct($model);
    }

     public function create(array $data)
     {
         return parent::create($data);
     }

     public function update(array $data, $id)
     {
         return $this->model->findOrFail($id)->update($data);
     }

    public function getUserWithOperatorId()
    {
       return $this->model->where('id', Auth::id() ?? 1)->first()->operator_id;
    }

    public function createUserWithTrainingSchoolRelation($school_code,$school_name,$phone, $email, $password,$training_school)
    {
         $this->model->operator_id = $school_code;
         $this->model->name = $school_name;
         $this->model->email = $email;
         $this->model->password = bcrypt($password);
         $this->model->user_status  = \App\Enums\UserStatus::Active;
         $this->model->user_role = 'training_school_admin';
         $this->model->phone = $phone;
         return $training_school->userTrainingSchool()->save($this->model);
    }


    public function updateTrainingSchoolRelation($id, $photo, $email, $name,  $training_school)
    {
        $user = $this->model->findOrFail($id);
        $user->photo = $photo;
        $user->email = $email;
        $user->name = $name;
        return $training_school->userTrainingSchool()->save($user);
    }

    public function updateTrainingSchoolRelationAndUserImage($id, $photo, $training_school)
    {
        $user = $this->model->findOrFail($id);
        $user->photo = $photo;
        return $training_school->userTrainingSchool()->save($user);
    }

    public function with($relations)
    {
        return $this->model->with($relations);
    }


    public function fetchAllSuperAdminAndAdminUser()
    {
        return $this->model->where([['user_role', 'super_admin'], ['user_role', 'admin']])->get();
    }

    public function changeUserPassword(int $id, string $password)
    {
        $users = $this->model->find($id);
        $users->password = bcrypt($password);
        $this->model->where( 'id' , auth()->user()->id)->update( array( 'password' =>  $users->password));
    }



    public function checkIfEmailExist(string $email,string $token)
    {
        if (!$this->model->where('email', $email)->first()) {
            return response()->json( 'Failed! email is not registered or doesnt exist.');
        }
        else{
            PasswordReset::insert(['email' => $email, 'token' => $token, 'created_at' => Carbon::now()]);
            $data = [
                'email' => $email,
                'token' => $token
            ];
            $user = $this->model->where('email', request()->email)->first();
            $user->notify(new ForgetPasswordNotification($data));
            return response()->json( 'We have emailed your password reset link!');
        }
    }


    public function passwordReset(string $email, string $token, string $password)
    {
        if(!PasswordReset::where(['email' => $email, 'token' => $token ])->first())
        {
            return response()->json( 'Error! Invalid token!', 404);
        }
        $this->model->where('email', $email)->update(['password' => bcrypt($password)]);
        PasswordReset::where(['email'=> $email])->delete();
        return response()->json("Password has been successfully changed");
    }

    public function selectUserBasedOnRole($filter)
    {
        return $this->model->where(function($query) use($filter)
        {
            foreach($filter as $value) $query->orWhere('user_role', $value);
        })
        ->get('operator_id');
    }


}
