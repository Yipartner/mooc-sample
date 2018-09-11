<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class HomeworkService{

    private $tableName = 'homework_user_relations';


    public function getFinishInfo(){

    }



    /*手动更新作业状态*/

    /**
     * 添加单条作业完成记录
     * @param int $userId
     * @param int $lessonId
     */
    public function finishOneHomework(int $userId,int $lessonId){
        DB::table($this->tableName)
            ->insert([
                'user_id'=>$userId,
                'lesson_id'=>$lessonId
            ]);
    }

    public function finishHomeworks($homework){
        DB::table($this->tableName)
            ->insert($homework);
    }

    /**
     * 批量删除作业完成记录
     * @param $homeworkId
     */
    public function removeFinishHomework(array $homeworkId){
        DB::table($this->tableName)
            ->whereIn('id',$homeworkId)
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
    public function isAccess($classNum,$classSecret) {
        $tClassSecret=DB::table('classes')
            ->where('class_num',$classNum)
            ->value('class_secret');
        if (md5($classSecret) == $tClassSecret){
            return true;
        }
        return false;
    }
}