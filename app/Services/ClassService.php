<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 18/9/11
 * Time: 下午7:37
 */

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ClassService
{
    private $tbName = 'classes';

    public function createClass($teacherId, $classInfo)
    {
        $time = new Carbon();
        $fullClassInfo = array_merge($classInfo, [
            'teacher_id' => $teacherId,
            'created_at' => $time,
            'updated_at' => $time,
            /** class14935210507BVc9v9ujP */
            'class_num' => 'class' . time() . str_random(10)
        ]);
        $classId = DB::table($this->tbName)->insertGetId($fullClassInfo);
        return $classId;
    }

    public function updateClass($classId, $classInfo)
    {
        $time = new Carbon();
        $classInfo['updated_at'] = $time;
        DB::table($this->tbName)->where('id', $classId)->update($classInfo);
    }

    public function getClassInfoById($classId)
    {
        $classInfo = DB::table($this->tbName)->where('id', $classId)->first();
        return $classInfo;
    }

    public function getClassListByTeacherId($teacherId)
    {
        $classesInfo = DB::table($this->tbName)->where('teacher_id', $teacherId)->get();
        return $classesInfo;
    }

    public function getAllClasses()
    {
        $classInfos = DB::table($this->tbName)->get();
        return $classInfos;
    }

    public function deleteClass($classId)
    {
//        todo
//        DB::transaction(function () use ($classId) {
//            DB::table($this->tbName)->where('id',$classId)->delete();
//        });
    }

    public function isOwner($classId, $userId)
    {
        $res = DB::table($this->tbName)->where('id', $classId)->value('teacher_id');
        return $res === $userId;
    }

    public function buyClassOrNot($classId, $userId)
    {
        $res = DB::table('user_class_relations')
            ->where([
                ['user_id', '=', $userId],
                ['class_id', '=', $classId],
            ])->first();
        return $res != null;
    }

    public function buyClass($userId, $classId)
    {
        $time = new Carbon();
        $res = DB::table('user_class_relations')->insertGetId([
            'user_id' => $userId,
            'class_id' => $classId,
            'created_at' => $time
        ]);
        return $res;
    }

    public function getClassByLessonId($lessonId){
        return DB::table('lessons')
            ->where('id',$lessonId)
            ->value('class_id');
    }

    public function isBuyOrNot($classId)
    {
        $res = $this->getClassBuyersNum($classId);
        return $res > 0;
    }

    public function getClassLessonNum($classId)
    {
        $lessonsNum = DB::table('lessons')->where('class_id', $classId)->count();
        return $lessonsNum;
    }

    public function getClassBuyersNum($classId)
    {
        $buyersNum = DB::table('user_class_relations')->where('class_id',$classId)->count();
        return $buyersNum;
    }
}