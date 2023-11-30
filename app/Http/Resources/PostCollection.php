<?php

namespace App\Http\Resources;

use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PostCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request):Collection
    {


         $return_post = $this->collection->transform(function ($post) {

            $name_attachments = collect(explode(",", $post->name_attachments))
                ->reject(function ($name) {
                return empty($name);
            });

            $path_attachments = collect(explode(",", $post->path_attachments))
                ->reject(function ($path) {
                return empty($path);
            });

            return collect([
                "id" => $post->id,
                "worker_name" => $post->worker_name,
                "content" => $post->content,
                "price" => $post->price,
                "status" => $post->status,
                "rejected_reason" => $post->rejected_reason,
                "attachments" => collect([
                    'name' => $name_attachments->isNotEmpty() ? $name_attachments : [],
                    'path' => $path_attachments->isNotEmpty() ? $path_attachments : [],
                ]),
            ]);
        });

        return $return_post;

    }
}
