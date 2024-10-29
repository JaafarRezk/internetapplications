<?php

namespace App\Aspects;

use App\Models\File;
use App\Models\FileLog;

class FileLoggingAspect extends Aspect
{

    public function __construct($message)
    {
        parent::__construct($message);
    }

    public function before()
    {
        FileLog::create([
            "date"=>date("Y-m-d H:i:s"),
            "operation"=>$this->message["function"],
            "file_id"=>null,
            "user_id"=>auth()->user() != null ? auth()->user()->id : null,
            "status"=>"Started",
        ]);
    }
    public function after(){

        if ($this->message["function"] == "checkIn" || $this->message["function"] == "checkOut")
        {
            FileLog::create([
                "date"=>date("Y-m-d H:i:s"),
                "operation"=>$this->message["function"],
                "file_id"=>$this->getFileId(),
                "user_id"=>auth()->user() != null ? auth()->user()->id : null,
                "status"=>"Finished",
            ]);

        }
        elseif($this->message["function"] == "bulkCheckIn" || $this->message["function"] == "uploadFiles")
        {
            if(!isset($this->message["response"]["data"]))
            {
                FileLog::create([
                    "date"=>date("Y-m-d H:i:s"),
                    "operation"=>$this->message["function"],
                    "file_id"=>null,
                    "user_id"=>auth()->user() != null ? auth()->user()->id : null,
                    "status"=>"Finished",
                ]);
            }
            else
            {
                foreach($this->message["response"]["data"] as $key=>$data)
                    FileLog::create([
                        "date"=>date("Y-m-d H:i:s"),
                        "operation"=>$this->message["function"],
                        "file_id"=>$this->getResponseFileId($key),
                        "user_id"=>auth()->user() != null ? auth()->user()->id : null,
                        "status"=>"Finished",
                    ]);
            }

        }

    }
    public function exception()
    {
        if ($this->message["function"] == "checkIn" || $this->message["function"] == "checkOut" )
        {
            FileLog::create([
                "date"=>date("Y-m-d H:i:s"),
                "operation"=>$this->message["function"],
                "file_id"=>$this->getFileId(),
                "user_id"=>auth()->user() != null ? auth()->user()->id : null,
                "status"=>"Exception",
            ]);
        }
        elseif($this->message["function"] == "bulkCheckIn" || $this->message["function"] == "uploadFiles")
        {
            if(!isset($this->message["response"]["data"]))
            {
                FileLog::create([
                    "date"=>date("Y-m-d H:i:s"),
                    "operation"=>$this->message["function"],
                    "file_id"=>null,
                    "user_id"=>auth()->user() != null ? auth()->user()->id : null,
                    "status"=>"Exception",
                ]);
            }
            else
            {
                foreach($this->message["response"]["data"] as $key=>$data)
                    FileLog::create([
                        "date"=>date("Y-m-d H:i:s"),
                        "operation"=>$this->message["function"],
                        "file_id"=>$this->getResponseFileId($key),
                        "user_id"=>auth()->user() != null ? auth()->user()->id : null,
                        "status"=>"Exception",
                    ]);
            }

        }


    }

    public function getFileId()
    {
        $id = "";
        if (!empty($this->message["urlParameters"]) || !empty($this->message["bodyParameters"]) || !empty($this->message["response"]["data"])) {
            if (!empty($this->message["urlParameters"]) && isset($this->message["urlParameters"]["id"])) {
                $id = $this->message["urlParameters"]["id"];
                return $id;
            } elseif (!empty($this->message["bodyParameters"]) && isset($this->message["bodyParameters"]["id"])) {
                $id = $this->message["bodyParameters"]["id"];
                return $id;
            }else{
                return null;
            }
        } else {
            return null;
        }
    }

    public function getResponseFileId($key){
        if (isset($this->message["response"]["data"][$key]) && isset($this->message["response"]["data"][$key]["id"])) {
            $id = $this->message["response"]["data"][$key]["id"];
            return $id;
        }else{
            return null;
        }
    }

}
