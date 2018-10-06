<?php

namespace App\Http\Controllers;

use App\Services\ClassService;
use App\Services\UserService;
use App\Tools\Code;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    private $classService;
    private $userService;
    private $classRule = [
        'name' => 'required',
        'content' => 'required',
        'fee' => 'required',
        'fee_back_num' => 'required',
        'class_pic' => ''
    ];

    public function __construct(ClassService $classService, UserService $userService)
    {
        $this->classService = $classService;
        $this->userService = $userService;
    }


    /**
     * 创建课程
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createClass(Request $request)
    {
        $userInfo = $request->user;
        $classRule = $this->classRule;
        $res = ValidationHelper::validateCheck($request->all(), $classRule);
        if ($res->fails())
            return response()->json([
                'code' => 201,
                'message' => $res->errors()
            ]);
        $classInfo = ValidationHelper::getInputData($request, $classRule);
        $classId = $this->classService->createClass($userInfo->id, $classInfo);
        return response()->json([
            'code' => 0,
            'message' => 'create class Success',
            'data' =>[
                'class_id' => $classId
            ]
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
        $res = ValidationHelper::validateCheck($request->all(), $rule);
        if ($res->fails())
            return response()->json([
                'code' => 201,
                'message' => $res->errors()
            ]);
        $classInfo = ValidationHelper::getInputData($request, $rule);
        $this->classService->updateClass($userInfo->id, $classInfo);
        return response()->json(Code::SUCCESS);
    }

    public function getClassInfo($id, Request $request)
    {
        $userInfo = $request->user;
        $classInfo = $this->classService->getClassInfoById($id);
        if(!$this->classService->isOwner($id,$userInfo->id))
        {
            unset($classInfo->access_token);
            unset($classInfo->class_secret);
            unset($classInfo->class_num);
            unset($classInfo->token_expired_at);
        }
        return response()->json([
            'code' => 0,
            'message' => 'get class success',
            'data' => $classInfo
        ]);
    }

    public function getAllClassList(Request $request)
    {
        $classList = $this->classService->getAllClasses();
        foreach ($classList as $classInfo)
        {
            unset($classInfo->access_token);
            unset($classInfo->class_secret);
            unset($classInfo->class_num);
            unset($classInfo->token_expired_at);
        }
        return response()->json([
            'code' => 0,
            'message' => 'get classes success',
            'data' => $classList
        ]);
    }

    public function getMyClassList(Request $request)
    {
        $userInfo = $request->user;
        $classList = $this->classService->getClassListByTeacherId($userInfo->id);
        return response()->json([
            'code' => 0,
            'message' => 'get my class success',
            'data' => $classList
        ]);
    }

    public function getClassLessons($id, Request $request)
    {
        $lessonsList = $this->classService->getClassLessons($id);
        return response()->json([
            'code' => 0,
            'message' => 'get my class success',
            'data' => $lessonsList
        ]);
    }

    public function buyClass($id, Request $request)
    {
        $userInfo = $request->user;

        if($this->classService->buyClassOrNot($id,$userInfo->id))
            return response()->json([
                'code' => 205,
                'message' => 'has buy class yet',
            ]);


        $classInfo = $this->classService->getClassInfoById($id);
        $res = $this->userService->delCoin($userInfo->id, $classInfo->fee);

        if($res < 0)
            return response()->json([
                'code' => 309,
                'message' => "扣费失败，余额不足"
            ]);
        $orderId = $this->classService->buyClass($userInfo->id,$id);
        return response()->json([
            'code' => 0,
            'message' => 'buy Success',
            'data' => [
                'order_id' => $orderId
            ]
        ]);
    }


    public function getMyBoughtClasses(Request $request)
    {
        $userInfo = $request->user;
        $classesList = $this->classService->getClassesByStuId($userInfo->id);
        foreach ($classesList as $classInfo)
        {
            unset($classInfo->access_token);
            unset($classInfo->class_secret);
            unset($classInfo->class_num);
            unset($classInfo->token_expired_at);
        }
        return response()->json([
            'code' => 0,
            'message' => 'get classes success',
            'data' => $classesList
        ]);
    }


}
