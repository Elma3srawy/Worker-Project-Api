<?php

namespace App\Jobs\reviews;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class avg_rate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected $review_id)
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $worker_id = DB::table("review_orders")
        ->leftJoin("order_posts" ,"review_orders.order_id" , "order_posts.id")
        ->leftjoin("posts"  ,"order_posts.post_id" , "posts.id")
        ->leftjoin("workers" , "posts.worker_id" , "workers.id")
        ->where("review_orders.id" , $this->review_id)
        ->value("workers.id");

        $average_rate = DB::table("review_orders")
        ->leftJoin("order_posts" , "review_orders.order_id" , "order_posts.id")
        ->leftjoin("posts"  , "order_posts.post_id" , "posts.id")
        ->leftjoin("workers" , "posts.worker_id" , "workers.id")
        ->where("workers.id" , $worker_id)
        ->average("review_orders.rate");

        DB::table("workers")->whereId($worker_id)->update(["rate" => Round($average_rate ,2)]);
    }
}
