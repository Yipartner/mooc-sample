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
    /***************************************************学生端*****************************************************/


    //TODO 获取学生所有课程的作业完成情况
    public function getStuAllClassHomeworkStatus(Request $request){

    }

    /**
     * 获取学生某一课程的作业完成情况
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStuHomeworkByClassId(Request $request){
        $classId = $request->input('class_id',null);
        if ($classId == null){
            return response()->json(Code::PARAM_ERROR);
        }
        $stu = $request->user;
        $homework = $this->homeworkService->getStuHomeworkByClassId($stu->id,$classId);
        $data =[];
        foreach ($homework as $item) {
            $h = [
                'id'=>$item->id,
                'name'=> $item->name,
            ];
            if (empty($item->id)){
                $h['status'] = false;
            }else{
                $h['status'] = true;
            }
            array_push($data,$h);
        }
        return response()->json([
            'code'=> 0,
            'message' => 'success',
            'data'=> [
                'homework'=> $data
            ]
        ]);

    }

    /**
     * 判断学生指定课时是否完成
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function isStuHomeworkFinish(Request $request){
        $lessonId = $request->input('homework_id',null);
        if ($lessonId == null){
            return response()->json(Code::PARAM_ERROR);
        }
        $stu = $request->user;
        $check = $this->homeworkService->isStuFinishHomework($stu->id,$lessonId);
        return response()->json([
            'code' => 0,
            'message' => 'success',
            'data'=> [
                'check' => $check
            ]
        ]);
    }

    /***************************************************教师端***************************************************/
    //TODO 获取教师某课程所有课时的作业完成情况
    public function getTeacherClassFinishStatus(Request $request){
        $classId = $request->input('class_id',null);
        if ($classId == null){
            return response()->json(Code::PARAM_ERROR);
        }
        $data = $this->homeworkService->getLessonFinishUserCountByClassId($classId);
        //todo 根据classId 获取 购买该课程的 user总数
        $num = 10;
        foreach ($data as $item){
            $item->rate = $item->finish_num / $num;
        }
        return response()->json([
            'code' => 0,
            'message' => 'success',
            'data'=>[
                'status'=> $data
            ]
        ]);
    }
    //TODO 获取某课时的完成人员名单
    public function getHomeworkFinishUser(Request $request){
        $lessonId = $request->input('lesson_id',null);
        if ($lessonId == null){
            return response()->json(Code::PARAM_ERROR);
        }
        $data = $this->homeworkService->getFinishUserByLessonId($lessonId);
        return response()->json([
            'code'=>0,
            'message' => 'success',
            'data' => [
                'users' => $data
            ]
        ]);
    }
    //TODO 获取某课时的未完成人员名单
    public function getHomeworkNoFinishUser(){

    }


    /*操作接口*/

    /**
     * 获取课程所有课时的作业的作业完成情况 todo 暂时不用
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getClassHomeworkStatus(Request $request){
        $teacher = $request->user;
        $classId = $request->input('classId',null);
        if ($classId==null){
            return response()->json(Code::PARAM_ERROR);
        }
        if (!$this->classService->isOwner($classId,$teacher->id)){
            return response()->json(Code::NO_PERMISSION);
        }
        $homeworkStatus=$this->homeworkService->getClassFinishInfo($classId);
        return response()->json([
            'code' => 0,
            'message' => 'success',
            'data'=>[
                'homework_status'=>$homeworkStatus
            ]
        ]);
    }

    /**
     * 获取单次课时的所有作业完成情况 todo 暂时不用
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLessonHomeworkStatus(Request $request){
        $teacher = $request->user;
        $lessonId = $request->input('lessonId',null);
        if ($lessonId == null){
            return response()->json(Code::PARAM_ERROR);
        }
        if (!$this->classService->isOwner($this->classService->getClassByLessonId($lessonId),$teacher->id)){
            return response()->json(Code::NO_PERMISSION);
        }
        $homeworkStatus = $this->homeworkService->getLessonFinishInfo($lessonId);
        return response()->json([
            'code'=>0,
            'message' => 'success',
            'data'=>[
                'homework_status'=>$homeworkStatus
            ]
        ]);
    }



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
