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

class LessonService
{
    private $tbName = 'lessons';

    public function createLesson($teacherId, $lessonInfo)
    {
        $time = new Carbon();
        $fullLessonInfo = array_merge($lessonInfo, [
            'teacher_id' => $teacherId,
            'created_at' => $time,
            'updated_at' => $time
        ]);
        $lessonId = DB::table($this->tbName)->insertGetId($fullLessonInfo);
        return $lessonId;
    }

    public function updateLesson($lessonId, $lessonInfo)
    {
        $time = new Carbon();
        $lessonInfo['updated_at'] = $time;
        DB::table($this->tbName)->where('id', $lessonId)->update($lessonInfo);
    }

    public function getLessonInfoById($lessonId)
    {
        $lessonInfo = DB::table($this->tbName)->where('id', $lessonId)->first();
        return $lessonInfo;
    }

    public function getLessonListByClassId($classId)
    {
        $lessonesInfo = DB::table($this->tbName)->where('class_id', $classId)->get();
        return $lessonesInfo;
    }

    public function deleteLesson($lessonId)
    {
        DB::table($this->tbName)->where('id', $lessonId)->delete();
    }

    public function updateLessionMovie($lessonId, $movieId)
    {
        DB::table($this->tbName)->where('id', $lessonId)->update([
            'movie_id' => $movieId
        ]);
    }
    public function getLessonOwner($lessonId){
        return DB::table($this->tbName)
            ->where('id',$lessonId)
            ->join('classes','classes.id','=','lessons.class_id')
            ->value('classes.teacher_id');
    }

    public function getLessonClassId($lessonId)
    {
        $classId = DB::table($this->tbName)->where('id', $lessonId)->value('class_id');
        return $classId;
    }
}