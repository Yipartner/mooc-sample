<?php

namespace App\Services;

use App\Tools\SqlTool;
use Carbon\Carbon;
use ClassesWithParents\D;
use Illuminate\Support\Facades\DB;

class HomeworkService
{

    private $tableName = 'homework_user_relations';


    public function isStuFinishHomework($userId, $lessonId)
    {
        if (
        DB::table($this->tableName)->where([
            ['lesson_id', '=', $lessonId],
            ['user_id', '=', $userId]
        ])->first()

        ) {
            return true;
        }
        return false;
    }

    public function getStuHomeworkByClassId($stuId, $classId)
    {
        $homework = DB::table('lessons')
            ->where('class_id', '=', $classId)
            ->Join($this->tableName, 'lessons.id', '=', $this->tableName . '.lesson_id')
            ->where($this->tableName . '.user_id', '=', $stuId)
            ->pluck('lessons.id');
        return $homework;
    }

    public function getLessonFinishUserCountByClassId($classId)
    {
        $data = DB::table('lessons')
            ->where('class_id', '=', $classId)
            ->join($this->tableName, 'lessons.id', '=', $this->tableName . '.lesson_id')
            ->select(DB::raw('lessons.*, count(*) as finish_num'))
            ->groupBy('lessons.id')
            ->get();
        return $data;
    }

    public function getUnFinishUserByLessonId($lessonId)
    {
        $classId = DB::table('lessons')
            ->where('id', '=', $lessonId)
            ->value('class_id');
        $stus = DB::table('user_class_relations')
            ->where('class_id','=',$classId)
            ->whereNotExists(function ($query)use ($lessonId){
                $query->select(DB::raw(1))
                    ->from('homework_user_relations')
                    ->where('homework_user_relations.lesson_id','=',$lessonId)
                    ->whereRaw('homework_user_relations.user_id = user_class_relations.user_id');
            })
            ->join('users','users.id','=','user_class_relations.user_id')
            ->select('users.*')
            ->get();
        foreach ($stus as $stu) {
            unset($stu->password);
            unset($stu->coin);
            unset($stu->is_stu);
            unset($stu->is_teacher);
        }
        return $stus;
    }

    public function getFinishUserByLessonId($lessonId)
    {
        $stus = DB::table($this->tableName)
            ->where('lesson_id', '=', $lessonId)
            ->join('users', $this->tableName . '.user_id', '=', 'users.id')
            ->select('users.*')
            ->get();
        foreach ($stus as $stu) {
            unset($stu->password);
            unset($stu->coin);
            unset($stu->is_stu);
            unset($stu->is_teacher);
        }
        return $stus;
    }

    /**
     * 获取课程整体作业完成情况 todo 暂时不用
     * @param $classId
     * @return mixed
     */
    public function getClassFinishInfo($classId)
    {
        $info = DB::table('lessons')
            ->where('class_id', $classId)
            ->join($this->tableName, 'lessons.id', '=', $this->tableName . '.lesson_id')
            ->select($this->tableName . '.*')
            ->get();
        return $info;
    }

    /**
     * 获取单节课时作业完成情况 todo 暂时不用
     * @param $lessonId
     * @return mixed
     */
    public function getLessonFinishInfo($lessonId)
    {
        $info = DB::table($this->tableName)
            ->where('lesson_id', $lessonId)
            ->get();
        return $info;
    }


    /*手动更新作业状态*/

    /**
     * 添加单条作业完成记录
     * @param int $userId
     * @param int $lessonId
     */
    public function finishOneHomework(int $userId, int $lessonId)
    {
        DB::table($this->tableName)
            ->insert([
                'user_id' => $userId,
                'lesson_id' => $lessonId,
                'created_at'=>Carbon::now()
            ]);
    }

    /**
     * 批量添加作业完成记录
     * @param $homework
     */
    public function finishManyHomework($homework)
    {
        DB::table($this->tableName)
            ->insert($homework);
    }

    /**
     * 批量删除作业完成记录
     * @param array $user
     * @param $lessonId
     */
    public function removeFinishHomework(array $user,$lessonId)
    {
        DB::table($this->tableName)
            ->whereIn('user_id', $user)
            ->where('lesson_id',$lessonId)
            ->delete();
    }

    /**
     * 删除单个作业完成记录
     * @param $user
     * @param $lessonId
     */
    public function removeOneHomework($user,$lessonId)
    {
        DB::table($this->tableName)
            ->where('user_id', $user)
            ->where('lesson_id',$lessonId)
            ->delete();
    }

    /*作业系统API接口*/


    /*辅助方法*/


    /**
     * 判断classNum与classSecret是否匹配
     * @param $classNum
     * @param $classSecret
     * @return boolean
     */
    public function isAccess($classNum, $classSecret)
    {
        $tClassSecret = DB::table('classes')
            ->where('class_num', $classNum)
            ->value('class_secret');
        if (md5($classSecret) == $tClassSecret) {
            return true;
        }
        return false;
    }

    /**
     * 为课程生成class_secret
     * @param $classId
     * @return string class_secret
     */
    public function generateSecret($classId)
    {
        $randSecret = SqlTool::getRandPass(12);
        DB::table('classes')
            ->where('id', $classId)
            ->update([
                'class_secret' => $randSecret
            ]);
        return $randSecret;
    }

    /**
     * 生成临时身份证access_token
     * @param $classId
     * @return string
     */
    public function makeAccessToken($classId)
    {
        $accessToken = SqlTool::getRandPass(32);
        DB::table('classes')
            ->where('id', $classId)
            ->update([
                'access_token' => $accessToken,
                'token_expired_at' => Carbon::now()->addMinutes(5)
            ]);
        return $accessToken;
    }

    /**
     * 根据class_num 获取 class_id
     * @param $classNum
     * @return mixed
     */
    public function getClassIdByClassNum($classNum)
    {
        return DB::table('classes')
            ->where('class_num', $classNum)
            ->value('id');
    }

    /**
     * 判断教师是否可以编辑作业完成记录
     * @param $teacherId
     * @param $homeworkId
     * @return bool
     */
    public function canTeacherEditHomework($teacherId, $homeworkId)
    {
        $trueTeacherId = DB::table($this->tableName)
            ->where($this->tableName.'.id', $homeworkId)
            ->join('lessons', 'lessons.id', '=', $this->tableName . '.lesson_id')
            ->join('classes', 'lessons.class_id', '=', 'classes.id')
            ->value('teacher_id');
        return ($trueTeacherId == $teacherId);
    }
}