<?php

namespace App\Http\Controllers\Client\OrderPost;

use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\OrderReviewRequest;
use App\Http\Resources\Reviews\reviewResource;
use App\Jobs\reviews\avg_rate;
use App\Traits\ApiResponses;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ReviewOrderController extends Controller
{
    use ApiResponses;
    public function store(OrderReviewRequest $request){
        try {
            $client_id = auth("client")->user()->id;
            $exist =  DB::table("review_orders")
            ->where("order_id" , $request->order_id);
            if($exist->exists()){
                return $this->errorResponse("You Rated Order Before");
            }

            $order = DB::table('order_posts')
            ->where("id" , $request->order_id)
            ->where("client_id" , $client_id);

            if(!$order->exists()){
                return $this->errorResponse("The selected order id is invalid.");
            }

            $validated  = collect($request->validated())->put("created_at" , Carbon::now());
            $review_id = DB::table("review_orders")->insertGetId($validated->all());

            avg_rate::dispatchAfterResponse($review_id);
            return $this->okResponse("Rated Successfully.");
        }
        catch (Exception $e) {
           return $this->errorResponse($e->getMessage());
        }
    }
    public function postRate(){

        try {
            $worker_id = auth('worker')->user()->id;

            $reviews = DB::table("review_orders")
            ->rightJoin("order_posts" , function ($join) use($worker_id) {
                $join->on("review_orders.order_id","order_posts.id")
                ->leftjoin("posts" , "order_posts.post_id" ,"posts.id")
                ->leftjoin("clients" , "order_posts.client_id" ,"clients.id")
                ->where("posts.worker_id" , $worker_id);
            })
            ->whereRaw("review_orders.order_id = order_posts.id")
            ->select(
                    "review_orders.id as review_id",
                    "review_orders.rate as rate",
                    "review_orders.comments as comment",
                    "clients.name As client_name",
                    "posts.content As content",
                )
            ->get();

            $rates = array_column($reviews->all(), 'rate');
            $averageRate = array_sum($rates) / count($rates);

            $collection = collect([
                'average_rate' => Round($averageRate , 2),
                'reviews' => $reviews,
            ]);
            return $this->successResponse($collection);

        }
        catch (Exception $e) {
           return $this->errorResponse($e->getMessage());
        }

    }
}
