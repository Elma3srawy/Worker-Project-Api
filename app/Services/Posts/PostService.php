<?php

namespace App\Services\Posts;

use App\Http\Requests\Notifications\CheckIDRequest;
use App\Http\Resources\PostCollection;
use App\Notifications\Admin\CreatePost;
use App\Notifications\Worker\ChangeStatus;
use App\Traits\ApiResponses;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Models\Admin;
use App\Models\Worker;
use App\Http\Requests\Posts\FiltePostRequest;

class PostService{
    use ApiResponses;

    public function GetAllPosts(){

        /* check the user  Admin or worker*/
        $query = auth("admin")->check() ? Null : "approved";


        $posts = DB::table("posts")
        ->leftJoin("attachments", function ($join) {
            $join->on("posts.id", "=", "attachments.attachable_id")
                ->where("attachments.attachable_type", "=", "App\Models\Post");
        })
        ->leftJoin("workers", function ($join) {
            $join->on("posts.worker_id", "=", "workers.id");
        })
        ->when($query, function ($q) use ($query) {
            $q->where("posts.status", "=", $query);
        })
        ->select(
            "posts.id",
            "workers.name as worker_name",
            "content",
            "price",
            "posts.status",
            DB::raw("IF(posts.status = 'rejected', rejected_reason, NULL) as rejected_reason"),
            DB::raw("GROUP_CONCAT(attachments.name) as name_attachments"),
            DB::raw("GROUP_CONCAT(attachments.path) as path_attachments"),
        )
        ->groupBy("posts.id", "workers.name", "content", "price", "posts.status" ,"rejected_reason")
        ->get();


        return (new PostCollection($posts));

    }

    public function SendAdminNotifications($post_id , $worker_id){

        $admins = Admin::get();
        $post   = DB::table("posts")->where("id" , $post_id)->get();
        $worker = DB::table("workers")->where("id" , $worker_id)->select('id','name' , 'email')->get();

        Notification::send($admins , new CreatePost($post,$worker));
    }


    public function ChangeStatusPost($request){

        $validated = $request->validated();

        $id = $validated['id'];

        unset($validated['id']);

        $validated['rejected_reason'] = $validated['status'] === "approved"
        ? Null :  $validated['rejected_reason'];


        DB::table("posts")->where("id" ,$id)->update($validated);

        return  $this->SendWorkerNotifications($id ,$validated);
    }


    public function SendWorkerNotifications($id ,$data){

        $post  = DB::table("posts")->where("id" , $id)->select("*")->first();

        $worker = Worker::find($post->worker_id);

        Notification::send($worker , new ChangeStatus($post));
    }
    public function postFilter($search){

        $result = DB::table('posts')
        ->leftJoin("workers" , 'posts.worker_id' , "workers.id")
        ->orwhere("posts.content" , "LIKE", "%$search%")
        ->orwhere("posts.price" , "LIKE", "%$search%")
        ->orwhere("workers.name" , "LIKE", "%$search%")
        ->where("posts.status" , "=" , "approved")
        ->select(
                "posts.id AS post_id",
                "workers.id AS worker_id",
                "workers.name AS worker_name",
                "posts.content",
                "posts.price",
                "posts.status",
                "posts.created_at",
            )
        ->get();

        return $result;
    }



}

