<?php

namespace App\Repository;

use App\Http\Requests\Orders\OrderServiceRequest;
use App\Interface\OrderServiceInterface;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Client\ChangeStatus;

class OrderServiceRepo implements OrderServiceInterface{

    use ApiResponses;
    public function AddOrder($request){
        $client_id = auth("client")->user()->id;
        $validated = collect($request->validated())->put('client_id', $client_id);

        $status= DB::table("posts")->where("id", $request->post_id)->where('status' , 'approved');
        if($status->count() <= 0){
            return $this->errorResponse("The post is not approved");;
        }

        $order = DB::table("order_posts")->where("client_id",$client_id)->where("post_id" , $request->post_id);

        if($order->exists()){
            return $this->errorResponse("You Are Add Order Before");
        }

        DB::table("order_posts")->insert($validated->all());
        return $this->okResponse("Order added successfully");
    }

    public function pendingOrder(){
        $worker_id = auth("worker")->user()->id;
        $order_pending = DB::table("order_posts")
        ->rightJoin("posts" ,function ($join) use($worker_id){
            $join->on("order_posts.post_id" ,"posts.id")
                ->where("posts.worker_id" , $worker_id)
                ->where("posts.status" , "approved");
        })
        ->leftJoin("clients" ,function ($join){
            $join->on("order_posts.client_id" ,"clients.id")
                ->whereRaw("clients.id = order_posts.client_id");
        })
        ->where("order_posts.status" , "pending")
        ->select(
                    "order_posts.id AS order_id" ,
                    'clients.id AS client_id' ,
                    "clients.name AS client_name" ,
                    'order_posts.status',
                    "posts.id AS post_id",
                    "posts.content",
                    "posts.price",
                )
        ->get();


        return $this->successResponse($order_pending);
    }

    public function workerChangeStatus($request){

        $worker_id = auth("worker")->user()->id;
        $order = DB::table("order_posts")
        ->rightJoin("posts" , function ($join) use($worker_id){
            $join->on("order_posts.post_id" , "posts.id")
            ->where("posts.worker_id" , $worker_id)
            ->where("posts.status" , "approved");
        })
        ->where("order_posts.status" , "pending")
        ->where("order_posts.id" ,$request->order_id)
        ->select("order_posts.id" , "order_posts.client_id")
        ->get();

        if($order->count() <= 0){
            return $this->errorResponse("The id not exist");
        }


        $id = $request->validated('order_id');
        $status = $request->validated('status');

        DB::table("order_posts")->where("id" ,$id)->update(['status' => $status]);

        // Send Ntification To Client When Worker Change Status Order
        $this->sendClientNotification($id , $order->pluck("client_id"));
        
        return $this->okResponse("Change status successfully.");

    }

    public function getMyOrder(){
        $client_id = auth("client")->user()->id;
        $MyOrders =DB::table("order_posts")
        ->leftJoin("posts" , "order_posts.post_id" ,  "posts.id")
        ->where("client_id" , $client_id)
        ->select("order_posts.id AS order_id" , "content"  ,"price" ,"order_posts.status")
        ->get();
        return $this->successResponse($MyOrders , "Get My Orders");
    }
    public function sendClientNotification($order_id , $client_id){
        $MyOrders =DB::table("order_posts")
        ->leftJoin("posts" , "order_posts.post_id" ,  "posts.id")
        ->where("order_posts.id" , $order_id)
        ->select("order_posts.id AS order_id" , "content"  ,"price" ,"order_posts.status")
        ->get();

        $client = Client::findOrFail($client_id);

        Notification::send($client ,new ChangeStatus($MyOrders));


    }

}


?>
