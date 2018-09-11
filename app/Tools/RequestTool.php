<?php

namespace App\Tools;

class RequestTool{

    static public function response($data,int $code,string $message){

        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data
        ]);

    }

}