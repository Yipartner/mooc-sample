<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VersionController extends Controller
{
    //

    public function hasNewVersion(Request $request){
        $currentVersion = $request->input('cversion');
        $newestVersion = DB::table('version_control')
                ->max('version_num');
        if ($currentVersion >= $newestVersion){
            return response()->json([
                'code' => 0,
                'message' => '你的版本是最新版本！',
                'data'=>[
                    'hasNew'=>false
                ]
            ]);
        }
        $versionContent = DB::table('version_control')
            ->where('version_num',$newestVersion)
            ->first();
        return response()->json([
            'code' => 0,
            'message' => '获取新版本内容成功',
            'data'=>[
                'hasNew'=> true,
                'version_content'=>$versionContent
            ]
        ]);

    }

    public function getNewestVersion(Request $request){
        $newestVersion = DB::table('version_control')
            ->max('version_num');
        $versionContent = DB::table('version_control')
            ->where('version_num',$newestVersion)
            ->first();
        return response()->json([
            'code' => 0,
            'message' => '获取新版本内容成功',
            'data'=>$versionContent
        ]);
    }
}
