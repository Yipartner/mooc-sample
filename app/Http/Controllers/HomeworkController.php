<?php

namespace App\Http\Controllers;

use App\Services\ClassService;
use App\Services\HomeworkService;
use App\Services\LessonService;
use App\Tools\Code;
use Illuminate\Http\Request;

class HomeworkController extends Controller
{
    //
    private $homeworkService;
    private $classService;
    private $lessonService;

    public function __construct(HomeworkService $homeworkService, ClassService $classService, LessonService $lessonService)
    {
        $this->homeworkService = $homeworkService;
        $this->classService = $classService;
        $this->lessonService = $lessonService;
    }

    /*手动接口*/

    /**
     * 添加单条作业完成记录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function finishOneHomework(Request $request)
    {
        $teacher = $request->user;
        $userId = $request->input('lessonUser', null);
        $lessonId = $request->input('lessonId', null);
        if ($userId == null || $lessonId == null) {
            return response()->json(Code::PARAM_ERROR);
        }
        if (!$this->classService->isOwner($this->lessonService->getLessonOwner($lessonId), $teacher->id)) {
            return response()->json(Code::NO_PERMISSION);
        }
        $this->homeworkService->finishOneHomework($userId, $lessonId);
        return response()->json(Code::SUCCESS);
    }

    /**
     * 添加指定作业的多条完成记录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function finishManyHomework(Request $request)
    {
        $teacher = $request->user;
        $lessonId = $request->input('lessonId', null);
        // $request->users (string) : "1,2,3,4,5"
        $users = $request->input('users', null);
        if ($lessonId == null || $users == null) {
            return response()->json(Code::PARAM_ERROR);
        }
        if (!$this->classService->isOwner($this->lessonService->getLessonOwner($lessonId), $teacher->id)) {
            return response()->json(Code::NO_PERMISSION);
        }
        $users = explode(',', $users);
        $data = [];
        foreach ($users as $user) {
            array_push($data, [
                'user_id' => $user,
                'lesson_id' => $lessonId
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
    public function removeHomework(Request $request)
    {
        $homeworkIds = $request->input('homework', null);
        if ($homeworkIds == null || !is_array($homeworkIds)) {
            return response()->json(Code::PARAM_ERROR);
        }
        $this->homeworkService->removeFinishHomework($homeworkIds);
        return response()->json(Code::SUCCESS);
    }

    public function removeOneHomework(Request $request){
        $teacher = $request->user;
        $homeworkId = $request->input('homework',null);
        if ($homeworkId == null){
            return response()->json(Code::PARAM_ERROR);
        }
        if (!$this->homeworkService->canTeacherEditHomework($teacher->id,$homeworkId)){
            return response()->json(Code::NO_PERMISSION);
        }
        $this->homeworkService->removeOneHomework($homeworkId);
        return response()->json(Code::SUCCESS);
    }

    /*API接口鉴权部分*/

    /**
     * 生成class_secret 只有一次返回，不提供查询接口，提示用户妥善保管
     * @param Request $request
     * @param $classId
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateClassSecret(Request $request, $classId)
    {
        $teacher = $request->user;
        if (!$this->classService->isOwner($classId, $teacher->id)) {
            return response()->json(Code::NO_PERMISSION);
        }
        $secret = $this->homeworkService->generateSecret($classId);
        return response()->json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                'class_secret' => $secret
            ]
        ]);
    }

    /**
     * 生成access_token
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateAccessToken(Request $request)
    {
        $class_num = $request->input('class_num', null);
        $class_secret = $request->input('class_secret', null);
        if ($class_num == null || $class_secret == null) {
            return response()->json(Code::PARAM_ERROR);
        }
        $classId=$this->homeworkService->getClassIdByClassNum($class_num);
        $teacher = $request->user;
        if (!$this->classService->isOwner($classId, $teacher->id)) {
            return response()->json(Code::NO_PERMISSION);
        }
        if (!$this->homeworkService->isAccess($class_num, $class_secret)) {
            return response()->json(Code::CLASS_OWNER_VALIDATE);
        }
        $accessToken = $this->homeworkService->makeAccessToken($classId);
        return response()->json([
            'code' => 0,
            'message' => 'success',
            'data' => [
                'access_token' => $accessToken
            ]
        ]);
    }

    /*对外开放API接口*/

    /**
     * 添加作业完成记录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addHomeworkApi(Request $request){
        $userId = $request->input('lessonUser', null);
        $lessonId = $request->input('lessonId', null);
        if ($userId == null || $lessonId == null) {
            return response()->json(Code::PARAM_ERROR);
        }
        if ($this->classService->getClassByLessonId($lessonId)!=$request->class->id){
            return response()->json(Code::NO_PERMISSION);
        }
        $this->homeworkService->finishOneHomework($userId,$lessonId);
        return response()->json(Code::SUCCESS);
    }
}
