<?php

namespace App\Tools;

use Illuminate\Support\Facades\DB;

class SqlTool
{
    static public function makeUUID()
    {
        $uuid = DB::select("select nextval('seed')")[0];
        return array_values(self::object_to_array($uuid))[0];
    }

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
}