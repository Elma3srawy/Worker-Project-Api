<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notifications\CheckIDRequest;
use App\Traits\ApiResponses;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    use ApiResponses;

    protected $guard_name;
    public function __construct(){
        $this->guard_name = guard_name(); // Get Current Guard Name
    }
    public function showAll(){
        $notifications = auth($this->guard_name)->user()->unreadNotifications;
        return $this->successResponse($notifications);
    }

    public function markAllAsRead(){
        $unreadNotifications  =  auth($this->guard_name)->user()->unreadNotifications;

        if ($unreadNotifications->isNotEmpty()) {

            $unreadNotifications->markAsRead();
            return $this->okResponse("Mark all as read notification successfully");

        } else {

            return $this->errorResponse("You dont have unread notification");
        }
    }

    public function destroyAll(){

        $user= auth($this->guard_name)->user();

        if($user->notifications->isNotEmpty()){

            $user->notifications()->delete();

            return $this->okResponse("Deleted notifications successfully.");

        }
        else {
            return $this->errorResponse("You dont have notifications");
        }
    }

    public function markOneAsRead(CheckIDRequest $request){

        try {
            DB::beginTransaction();

            $row = DB::table("notifications")
            ->where("id" , $request->id)
            ->whereNull("read_at")
            ->update(["read_at" => Carbon::now()]);


            if($row <= 0){
                return $this->errorResponse("Notification marked as read.");
            }

            DB::commit();

            return $this->okResponse("Notification marked as read successfully.");
        }
        catch (Exception $e) {
            DB::rollback();
            return $this->errorResponse($e->getMessage());
        }
    }


}
