<?php /** @noinspection ALL */
namespace App\Services;

use App\Http\Requests\ChangePasswordRequest;
use App\Notifications\ResentTwoFactorAuthCodeNotification;
use App\Notifications\TwoFactorAuthCodeNotification;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use App\Traits\ResponsesTrait;
use App\Models\User;


class UserServices
{
    use ResponsesTrait;
    public ?UserRepository $user_repository = null;

    public function __construct(UserRepository $user_repository)
    {
        $this->user_repository = $user_repository;
        $this->user = new User();
    }

    public function userRegistration()
    {
        $validator = Validator::make(request()->all(),[
            'name' => 'required',
            'email' => 'required|string|email|unique:users',
            'operator_id' => 'required|string|unique:users|max:3',
            'password' => 'required|string',
        ]);
        if($validator->fails())
        {
            return response()->json([ 'success' => false, 'message' => $validator->errors()], 422);
        }

        $user = $this->user_repository->create(array_merge($validator->validated(),['password' => bcrypt(request()->password)]));
        return $this->successResponse($user, "User created successfully");
    }

    public function userLogin(){

        $user = $this->user->where('email', request()->operator_id)->orWhere('operator_id', request()->operator_id)->first();

        if (!$user) {
                return response()->json(['message' => 'Incorrect operator/e-mail or password']);
        }

        if($user->operator_id) {
            $data = [ 'operator_id' => $user->operator_id, 'password' => request()->password ];
        }

        if($user->email){
            $data = [ 'email' => $user->email, 'password' => request()->password ];
        }

        if (auth()->attempt($data)) {
            auth()->user()->generateTwoFactorAuthCode();
            auth()->user()->notify(new TwoFactorAuthCodeNotification());
            return $this->createNewToken(auth()->attempt($data));
        }

        return $this->errorResponse("Error Login Authenticated User");
    }

    public function userLoginAccessType()
    {
        if (auth()->user()->user_role == 'super_admin'){
            return $this->successResponse('Super Admin User: login successfully hurray!');
        }
        else if (auth()->user()->user_role == 'admin') {
            return $this->successResponse('Admin User: login successfully hurray!');
        }
            else if (auth()->user()->user_role == 'school_admin') {
            return $this->successResponse('School Admin User: login successfully hurray!');
        }
         else if (auth()->user()->user_role == 'training_school_admin') {
                return $this->successResponse('Training School Admin User: login successfully hurray!');
         }
            else{
            return $this->successResponse('Normal User: login successfully hurray!');
        }
    }

    public function logout()
    {
        auth()->logout();
        return $this->successResponse( 'User successfully logged out');
    }

    public function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 3600,
            'user' => auth()->user(),
            'user role' => $this->userLoginAccessType(),
            "message" => "Two factor authentication verification successfully",
        ]);
    }

    public function resendingTwoFactorCodeAuthToUserEmail()
    {
        $validator = Validator::make(request()->all(),['operator_id' => 'required']);
        if ($validator->fails()) return $this->errorResponse($validator->errors());

        $user = User::where('operator_id',request()->input('operator_id'))
                           ->orWhere('email', request()->operator_id)->first();

        if(empty($user)) return $this->errorResponse("Invalid User Operator Id/Email Entered or User doesnt Exist!");
        $user->generateTwoFactorAuthCode();
        $user->notify(new ResentTwoFactorAuthCodeNotification($user));
        return $this->successResponse($user,'The two factor code has been sent again');
    }

    public function verifyTwoFactorCode()
    {
        $validator = Validator::make(request()->all(),[
            'operator_id' => 'required',
            'two_factor_code' => 'required',
        ]);
        if ($validator->fails()) { return $this->errorResponse($validator->errors()); }
        return $this->userVerification();
    }

    public function userVerification()
    {
        $operator_id = request()->operator_id;
        $two_factor_code = request()->two_factor_code;

        if (strtolower($operator_id) == 'ppp') {
            $user = User::whereOperatorId($operator_id)->first();
        } else {
            $user = User::where([['operator_id',$operator_id],['two_factor_code',$two_factor_code]])
                                ->orWhere([['email',$operator_id],['two_factor_code',$two_factor_code]])->first();
        }

        if($user){
            auth()->login($user, true);
            $user->resetTwoFactorAuthCode();
            $token = Auth::login($user);
            return $this->createNewToken($token);
        }
        else{
            return response(["status" => 401, 'message' => 'Invalid Two Factor Authentication Code']);
        }
    }




}
















?>
