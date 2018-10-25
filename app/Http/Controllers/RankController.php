<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RankController extends Controller
{
    //

    /**
     * 获取某课程的完成度rank
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRankByClass(Request $request)
    {
        $class = $request->input('classId');
        $lessons = DB::table('lessons')
            ->where('class_id', $class)
            ->distinct()
            ->pluck('id');
        $len = count($lessons);
        $res = DB::table('homework_user_relations')
            ->whereIn('lesson_id', $lessons)
            ->groupBy('user_id')
            ->select(DB::raw('user_id,count(*) as finish_num,users.name'))
            ->orderBy('finish_num','desc')
            ->join('users','homework_user_relations.user_id','=','users.id')
            ->get()->toArray();

        foreach ($res as $k=>$v){
            $v->rante = round($v->finish_num/$len,2);
            $v->rante = sprintf("%.2f%%",$v->finish_num/$len*100);
        }
        return response()->json([
            'code'=>0,
            'message'=>'获取课程完成度排行榜成功',
            'data'=>$res
        ]);
    }

}
