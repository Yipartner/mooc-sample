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
            'updated_at' => $time
        ]);
        $classId = DB::table($this->tbName)->insertGetId($fullClassInfo);
        return $classId;
    }

    public function updateClass($classId, $classInfo)
    {
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

    //todo BuyClass
}