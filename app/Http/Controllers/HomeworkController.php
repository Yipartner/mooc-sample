<?php

namespace App\Http\Controllers;

use App\Services\HomeworkService;
use App\Tools\Code;
use Illuminate\Http\Request;

class HomeworkController extends Controller
{
    //
    private $homeworkService;

    public function __construct(HomeworkService $homeworkService)
    {
        $this->homeworkService= $homeworkService;
    }

    /*手动接口*/

    /**
     * 添加单条作业完成记录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function finishOneHomework(Request $request){
        $userId = $request->input('homework_user',null);
        $lessonId = $request->input('homework_id',null);
        if ($userId ==null || $lessonId == null){
            return response()->json(Code::PARAM_ERROR);
        }
        $this->homeworkService->finishOneHomework($userId,$lessonId);
        return response()->json(Code::SUCCESS);
    }

    /**
     * 添加指定作业的多条完成记录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function finishManyHomework(Request $request){
        $lessonId = $request->input('lessonId',null);
        // $request->users (string) : "1,2,3,4,5"
        $users = $request->input('users',null);
        if ($lessonId == null || $users == null){
            return response()->json(Code::PARAM_ERROR);
        }
        $users = explode(',',$users);
        $data = [];
        foreach ($users as $user){
            array_push($data,[
                'user_id'=>$user,
                'lesson_id'=>$lessonId
            ]);
        }
        $this->homeworkService->finishManyHomework($data);
        return response()->json(Code::SUCCESS);
    }

    /**
     * 批量移除作业完成记录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeHomework(Request $request){
        $homeworkIds = $request->input('homework',null);
        if ($homeworkIds == null|| !is_array($homeworkIds)){
            return response()->json(Code::PARAM_ERROR);
        }
        $this->homeworkService->removeFinishHomework($homeworkIds);
        return response()->json(Code::SUCCESS);
    }




}
