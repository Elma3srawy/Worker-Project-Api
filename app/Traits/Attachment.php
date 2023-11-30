<?php

namespace App\Traits;


use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait Attachment
{

    use ApiResponses;
    public function uploadFile($files, $Storage , $type , $attachable_id , $attachable_type){
        try {
            DB::beginTransaction();

            foreach ($files as $file){
                $path = $file->store($Storage);
                $name = $file->getClientOriginalName();
                DB::table("attachments")->insert([
                    "type" => $type,
                    "name" =>$name,
                    "path" => $path,
                    "attachable_id" => $attachable_id,
                    "attachable_type" => $attachable_type,
                    "created_at" => Carbon::now(),
                ]);
            }

            DB::commit();
        }
        catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }


    public function updateFile($files , $Storage , $type , $attachable_id , $attachable_type){
        try {
            DB::beginTransaction();

            $file = DB::table("attachments")
            ->where("attachable_id" , $attachable_id)
            ->where("attachable_type" , $attachable_type)
            ->select("id" , "path")
            ->get();

            $ids = $file->pluck("id");
            $paths = $file->pluck("path");

            $this->deleteFile($ids , $paths);

            foreach ($files as $file) {
                $path = $file->store($Storage);
                $name = $file->getClientOriginalName();
                DB::table("attachments")->whereIn("id" ,$ids)
                 ->updateOrInsert([
                    "type" => $type,
                    "name" =>$name,
                    "path" => $path,
                    "attachable_id" => $attachable_id,
                    "attachable_type" => $attachable_type,
                    "updated_at" => Carbon::now(),
                ]);
            }


            DB::commit();
        }
        catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }


    public function deleteFile($ids , $paths ){
        try {
            DB::beginTransaction();
            foreach ($paths as $path) {
                if ($path !== null && Storage::exists($path)) {
                    Storage::delete($path);
                }
            }
            DB::table("attachments")->whereIn("id" , $ids)->delete();

            DB::commit();
        }
        catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }






}

?>
