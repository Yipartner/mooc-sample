<?php

namespace App\Tools;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SqlTool
{
    static public function object_to_array($obj)
    {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)object_to_array($v);
            }
        }
        return $obj;
    }

    static public function getRandPass($length = 6)
    {
        $password = '';
        //将你想要的字符添加到下面字符串中，默认是数字0-9和26个英文字母
        $chars = "23456789abcdefghjkmnopqrstuvwxyABCDEFGHJKMNPQRSTUVWXYZ";
        $char_len = strlen($chars);
        for ($i = 0; $i < $length; $i++) {
            $loop = mt_rand(0, ($char_len - 1));
            //将这个字符串当作一个数组，随机取出一个字符，并循环拼接成你需要的位数
            $password .= $chars[$loop];
        }
        return $password;
    }

    static public function getUUToken(){
        $uuStr = md5(time()).self::getRandPass();
        return $uuStr;
    }

}