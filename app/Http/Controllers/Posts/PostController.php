<?php

namespace App\Http\Controllers\Posts;

use App\Http\Controllers\Controller;
use App\Http\Requests\posts\ChangeStatusRequest;
use App\Http\Requests\Posts\destroyRequest;
use App\Http\Requests\Posts\StoreRequest;
use App\Http\Requests\Posts\UpdateRequest;
use App\Traits\ApiResponses;
use App\Traits\Attachment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\Posts\PostService;
use App\Http\Requests\Posts\FiltePostRequest;


class PostController extends Controller
{
    use Attachment,ApiResponses;
    /**
     * Display a listing of the resource.
     */
    public function __construct(protected PostService $post){}
    public function index()
    {
        $posts = $this->post->GetAllPosts();
        return $this->successResponse($posts);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        try{

            DB::beginTransaction();
            $validated = $request->validated();
            $validated["worker_id"] = auth("worker")->user()->id;
            $validated["created_at"] = Carbon::now();

            $id = DB::table("posts")->insertGetId($validated);

            if ($request->hasFile('photos')){
                $photos = $request->file("photos");
                $this->uploadFile($photos,"Posts" , "Post_image" , $id ,"App\Models\Post");
            }

            $this->post->SendAdminNotifications($id , $validated["worker_id"]);


            DB::commit();
            return $this->okResponse("post created succssfully");
        }catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }


    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request)
    {
        try {

            DB::beginTransaction();

            $validated = $request->validated();
            $validated["updated_at"] = Carbon::now();

            DB::table("posts")
            ->where("id" , $request->id)
            ->update($validated);

            if ($request->hasFile("photos")){

                $photos = $request->file("photos");
                $this->updateFile($photos, "Posts" , "Post_image" ,$request->id , "App\Models\Post");
            }

            DB::commit();
            return $this->okResponse("successfully updated post");
        }
        catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(destroyRequest $request)
    {
        try {
            DB::beginTransaction();
                $posts = DB::table("posts")
                ->where("posts.id", $request->id)
                ->leftJoin("attachments", function ($join) {
                    $join->on("posts.id", "=", "attachments.attachable_id")
                        ->where("attachments.attachable_type", "=", "App\Models\Post");
                })
                ->select(
                    "posts.id",
                    "attachments.id as ids",
                    "attachments.path",
                )
                ->get();


                $ids = $posts->pluck("ids")->toArray();
                $paths = $posts->pluck("path")->toArray();
                $this->deleteFile($ids , $paths);

                DB::table("posts")->delete($posts->pluck("id")->first());

            DB::commit();

            return $this->okResponse("successfully deleted post");
        }
        catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function ChangeStatusPost(ChangeStatusRequest $request){
        try {
            $this->post->ChangeStatusPost($request);
            return $this->okResponse("Changed Status Post Successfully");
        }
        catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }
    public function postFilter(FiltePostRequest $request){
        try {
            $result = $this->post->postFilter($request->search);
            return $this->successResponse($result);
        }
        catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

}
