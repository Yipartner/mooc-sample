<?php

namespace App\Http\Controllers;

use App\Services\ClassService;
use App\Tools\Code;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    private $classService;
    private $classRule = [
        'name' => 'required',
        'content' => 'required',
        'fee' => 'required',
        'fee_back_num' => 'required'
    ];

    public function __construct(ClassService $classService)
    {
        $this->classService = $classService;
    }


    /**
     * 创建课程
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createClass(Request $request)
    {
        $userInfo = $request->user;
        if (!$userInfo->is_teacher)
            return response()->json(Code::NO_PERMISSION);
        $classRule = $this->classRule;
        $classInfo = ValidationHelper::getInputData($request, $classRule);
        $classId = $this->classService->createClass($userInfo->id, $classInfo);
        return response()->json([
            'code' => 0,
            'message' => 'create class Success',
            'class_id' => $classId
        ]);
    }

    public function updateClass($id, Request $request)
    {
        $userInfo = $request->user;

        $rule = $this->classRule;
        if ($this->classService->isBuyOrNot($id)) {
            unset($rule['fee']);
            unset($rule['fee_back_num']);
        }
        if (!$this->classService->isOwner($id, $userInfo->id))
            return response()->json(Code::NO_PERMISSION);
        $classInfo = ValidationHelper::getInputData($request, $rule);
        $this->classService->createClass($userInfo->id, $classInfo);
        return response()->json(Code::SUCCESS);
    }

    public function getClassInfo($id, Request $request)
    {
        $classInfo = $this->classService->getClassInfoById($id);
        return response()->json([
            'code' => 0,
            'message' => 'get class success',
            'class_info' => $classInfo
        ]);
    }

    public function getAllClassList(Request $request)
    {
        $classList = $this->classService->getAllClasses();
        return response()->json([
            'code' => 0,
            'message' => 'get classes success',
            'class_list' => $classList
        ]);
    }

    public function getMyClassList(Request $request)
    {
        $userInfo = $request->user;
        if (!$userInfo->is_teacher)
            return response()->json(Code::NO_PERMISSION);
        $classList = $this->classService->getClassListByTeacherId($userInfo->id);
        return response()->json([
            'code' => 0,
            'message' => 'get my class success',
            'class_list' => $classList
        ]);
    }


}
