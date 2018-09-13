<?php

namespace App\Tools;

class Code{
    const  SUCCESS = [
        'code' => 0,
        'message' => 'success'
    ];
    const NEED_ACCESS_TOKEN = [
        'code'=>101,
        'message'=>'need access_token'
    ];
    const ACCESS_TOKEN_EXPIRED = [
        'code'=>102,
        'message'=>'access_token expired'
    ];
    const PARAM_ERROR = [
        'code' => 103,
        'message'=> 'params not right'
    ];
}