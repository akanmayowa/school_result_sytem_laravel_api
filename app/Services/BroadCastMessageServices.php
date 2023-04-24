<?php /** @noinspection ALL */

namespace App\Services;

use App\Events\MessageSentToWahebAdminEvent;
use App\Events\MessageSentToAllTrainingSchoolEvent;
use App\Events\MessageSentToSchoolBasedOnSelectedCoursesEvent;
use App\Events\MessageSentToSelectedTrainingSchoolEvent;
use App\Models\CourseHeader;
use App\Models\TrainingSchool;
use App\Models\User;
use App\Notifications\AdminNotification;
use App\Repositories\CourseModuleRepository;
use App\Repositories\TrainingSchoolRepository;
use App\Repositories\UserRepository;
use App\Traits\ResponsesTrait;
use App\Repositories\MessageRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Message;
use App\Repositories\CourseHeaderRepository;

class BroadCastMessageServices
{
    use ResponsesTrait;
    public ? MessageRepository $message_repository = null;

    public function __construct(MessageRepository $message_repository)
    {
       $this->message_repository = $message_repository;
    }

     public function fetchAllUserMessage()
     {
        $messages = $this->message_repository->get();
        return $this->successResponse($messages,"User Message Retrieved Successfully !");
     }

    public function sentMessageToAllTrainingSchool()
     {
         $validator = Validator::make(request()->all(), ['message' => 'required|string', 'title' => 'required|string',]);
         if($validator->fails()) { return $this->errorResponse($validator->errors()); }
         $message = auth()->user()->messages()->create(array_merge($validator->validated(),
             ['school_code' => json_encode(TrainingSchool::get('school_code')) ],
             ['reciever_id' => json_encode(TrainingSchool::get('id')) ],
             ['operator_id' => auth()->user()->operator_id]
         ));
         broadcast(new MessageSentToAllTrainingSchoolEvent($message, new TrainingSchool()))->toOthers();
         $title = "BroadCast Notification";
         $body = "BroadCast Message Was Sent to All Training School";
         $actionType = "candidate_indexed";
         auth()->user()->notify(new AdminNotification($title,$body,$actionType));
         return $this->successResponse($message,'Message Sent Successfully!');
     }

     public function sendMessageToSelectedTrainingSchool()
     {
         $validator = Validator::make(request()->all(), [
             'message' => 'required|string',
             'school_code' => 'required',
             'title' => 'required|string',
         ]);
         if($validator->fails()) {
             return $this->errorResponse($validator->errors());
         }
         $filter = explode(",", request()->school_code);
         $training_school =  (new TrainingSchoolRepository(new TrainingSchool()))->selectTrainingschoolwithSchoolCode($filter);
         $message = auth()->user()->messages()->create(array_merge($validator->validated(),
             ['reciever_id' => json_encode($training_school)  ],
             ['school_code' => json_encode($training_school) ],
             ['operator_id' => auth()->user()->operator_id]
         ));
         broadcast(new MessageSentToSelectedTrainingSchoolEvent($message, new TrainingSchool($training_school->toArray())))->toOthers();
         $title = "BroadCast Notification";
         $body = "A BroadCast Message Was Sent To Selected Training School";
         $actionType = "candidate_indexed";
         auth()->user()->notify(new AdminNotification($title,$body,$actionType));
         return $this->successResponse($message,'Message Sent Successfully!');
     }

     public function sendMessageToTrainingSchoolBasedOnSelectedCourses()
     {
         $validator = Validator::make(request()->all(), ['message' => 'required|string', 'header_key' => 'required|string',  'title' => 'required|string' ]);
         if($validator->fails()){
             return $this->errorResponse($validator->errors());
         }
         $filter = explode(",", request()->header_key);
         $index_code = (new CourseHeaderRepository(new CourseHeader()))->selectCourseHeaderWithHeaderKey($filter);
         $training_school = [];
         foreach ($index_code as $value) $training_school[] = (new TrainingSchoolRepository(new TrainingSchool()))->selectIndexCodeAndSchoolCodeUsingQuery($value);
         if(count($training_school) == 0) return response()->json("the course selected not found in any training school");
         $message = auth()->user()->messages()->create(array_merge($validator->validated(),
                                         ['school_code' => json_encode($training_school)], ['operator_id' => auth()->user()->operator_id]
         ));
         broadcast(new MessageSentToSchoolBasedOnSelectedCoursesEvent($message, new TrainingSchool($training_school)))->toOthers();
         $title = "BroadCast Notification";
         $body = "A BroadCast Message Was Sent To Selected Training School";
         $actionType = "candidate_indexed";
         auth()->user()->notify(new AdminNotification($title,$body,$actionType));
         return $this->successResponse($message,'Message Sent Successfully!');
     }

     public function sendMessageToWahebAdmin()
     {
         $validator = Validator::make(request()->all(), ['message' => 'required|string',  'title' => 'required|string']);
         if($validator->fails()) {
             return $this->errorResponse($validator->errors());
         }
         $filter =  ['super_admin','admin','school_admin'];
         $user = (new UserRepository(new User()))->selectUserBasedOnRole($filter);
         if($user->isEmpty()) return response()->json("No Waheb Admin User found ");
         $message = auth()->user()->messages()->create(array_merge($validator->validated(),
             ['reciever_id' => json_encode($user)], ['operator_id' => auth()->user()->operator_id]));
         broadcast(new MessageSentToWahebAdminEvent($message, new User($user->toArray())))->toOthers();
         $title = "BroadCast Notification";
         $body = "A BroadCast Message Was Sent To Waheb Admin";
         $actionType = "candidate_indexed";
         auth()->user()->notify(new AdminNotification($title,$body,$actionType));
         return $this->successResponse($message,'Message Sent Successfully!');
     }

     public function fetchAllBroadCastForAdmin()
     {
         $messages = (new Message())->all()->sortByDesc('created_at');
         $result = [];
         foreach ( $messages as $message){
             if($message->reciever_id != NULL)
             {
                 $receivers = json_decode( $message['reciever_id']);
             foreach($receivers  as $receiver)
             {
                $datum = $receiver->operator_id ?? NULL;
                 if($datum == auth()->user()->operator_id){
                  $result[] = [
                      'id' => $message->id,
                      'title' => $message->title,
                      'message'=> $message->message,
                      'sender' => $message->operator_id,
                      'date' => $message->created_at,
                  ];
                 }
             }
             }
         }

         return $this->successResponse($result);
     }

    public function fetchAllBroadCastForSchoolAdmin()
    {
        $messages = Message::all()->sortByDesc('created_at');
        $result = [];
        foreach ( $messages as $message){
            if($message->school_code != NULL)
            {
                $receivers = json_decode($message['school_code']);
                foreach($receivers  as $receiver)
                {
                    $datum = $receiver->school_code ?? NULL;
                    if($datum == auth()->user()->operator_id){
                        $result[] = [
                            'id' => $message->id,
                            'title' => $message->title,
                            'message'=> $message->message,
                            'sender' => $message->operator_id,
                            'date' => $message->created_at,
                        ];
                    }
                }
            }
        }
        return $this->successResponse($result);
    }

     public function changeBroadCastMessageReadOrUnread()
     {
           $message = Message::where('id', request()->id)->first();
           if(!$message){
               return $this->errorResponse("Message ID Doesnt Exist");
           }

           if($message->status == 1){
               $message->update(['status' => 0]);
               return $this->successResponse($message, "Changed To Unread Message");
           }
           if($message->status == 0) {
               $message->update(['status' => 1]);
               return $this->successResponse($message, "Changed To Read Message");
           }
     }

     public function getSingleMessage()
     {
         $messages = Message::where('id', request()->id)->first();
         if(empty($messages)){
             return $this->errorResponse("Invalid ID Or ID Doesnt Exist");
         }
         $result = [];
         foreach (array($messages) as $message)
         {
                 $result[] = [
                     'id' => $message->id,
                     'title' => $message->title,
                     'message'=> $message->message,
                     'sender' => $message->operator_id,
                     'date' => ($message->created_at)->format('y-m-d H:m') ?? NULL,
                 ];
         }
         return $this->successResponse($result, "Single Message Retrieved SuccessFully");
     }

     public function getSingleNotification()
     {
         $notifications = DB::table('notifications')->where('id', request()->id)->first();

         if(empty($notifications)){
             return $this->errorResponse("Invalid ID Or ID Doesnt Exist");
         }
         return response()->json(['data' => $notifications, 'message' => "Single Notification Retrieved SuccessFully"],202);
     }




}
