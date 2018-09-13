<?php

namespace App\Http\Controllers;

use App\Services\ClassService;
use App\Tools\Code;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    private $classService;

    public function __construct(ClassService $classService)
    {
        $this->classService = $classService;
    }


    public function createClass(Request $request)
    {
        $userInfo = $request->user;
        if(!$userInfo->is_teacher)
            return response()->json(Code::NO_PERMISSION);
        $classRule  = [
            'name' => 'required',
            'content' => 'required',
            'fee' => 'required',
            'fee_back_num' => 'required'
        ];
        $classInfo = ValidationHelper::getInputData($request, $classRule);
        $classId = $this->classService->createClass($userInfo->id,$classInfo);
        return response()->json([
            'code' => 0,
            'message' => 'create class Success',
            'class_id' => $classId
        ]);
    }
}
