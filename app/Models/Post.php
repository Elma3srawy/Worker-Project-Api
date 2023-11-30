<?php

namespace App\Models;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;


    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
    public function Worker()
    {
        return $this->belongsTo(Worker::class , "worker_id");
    }
}
