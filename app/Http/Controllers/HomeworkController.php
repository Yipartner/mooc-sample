<?php

namespace App\Http\Controllers;

use App\Services\HomeworkService;
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


    public function finishOneHomework(Request $request){
        $userId = $request->input('homework_user',null);
        $lessonId = $request->input('homework_id',null);
        if ($userId ==null || $lessonId == null){
            return response()->json([
                'code' => 102,
                'message' => 'params not right'
            ]);
        }
        $this->homeworkService->finishOneHomework($userId,$lessonId);
        return response()->json([
            'code' => 0,
            'message' => 'success'
        ]);
    }

}
