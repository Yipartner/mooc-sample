<?php

namespace App\Http\Controllers;

use App\Services\ClassService;
use App\Services\LessonService;
use App\Services\UserService;
use App\Tools\Code;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    private $lessonService;
    private $userService;
    private $classService;

    public function __construct(LessonService $lessonService,UserService $userService, ClassService $classService)
    {
        $this->lessonService = $lessonService;
        $this->userService = $userService;
        $this->classService = $classService;
    }

    public function createLesson(Request $request)
    {
        $userInfo = $request->user;
        $rules = [
            'name' => 'required',
            'class_id' => 'required',
            'content' => '',
            'movie_id' => '',
            'homework_content' => '',
            'homework_type' => 'required'
        ];

        if(!$this->classService->isOwner($request->class_id,$userInfo->id))
            return response()->json(Code::NO_PERMISSION);
        $res = ValidationHelper::validateCheck($request->all(), $rules);
        if($res->fails())
            return response()->json([
                'code' => 201,
                'message' => $res->errors()
            ]);
        $lessonInfo = ValidationHelper::getInputData($request, $rules);
        $lessonId = $this->lessonService->createLesson($userInfo->id,$lessonInfo);
        return response()->json([
            'code' => 0,
            'message' => 'create lesson Success',
            'class_id' => $lessonId
        ]);
    }

    public function updateLesson($id, Request $request)
    {
        $userInfo = $request->user;
        $rules = [
            'name' => '',
            'content' => '',
            'movie_id' => '',
            'homework_content' => '',
            'homework_type' => ''
        ];
        $classId = $this->lessonService->getLessonClassId($id);
        if(!$this->classService->isOwner($classId,$userInfo->id))
            return response()->json(Code::NO_PERMISSION);
        $res = ValidationHelper::validateCheck($request->all(), $rules);
        if($res->fails())
            return response()->json([
                'code' => 201,
                'message' => $res->errors()
            ]);
        $lessonInfo = ValidationHelper::getInputData($request, $rules);
        $this->lessonService->updateLesson($id,$lessonInfo);
        return response()->json(Code::SUCCESS);
    }





}
