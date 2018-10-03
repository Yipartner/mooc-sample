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

    public function createLesson($lessonInfo)
    {
        $time = new Carbon();
        $fullLessonInfo = array_merge($lessonInfo, [
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
        $lessonInfo = DB::table($this->tbName)
            ->where($this->tbName.'.id', $lessonId)
            ->leftJoin('medias','medias.id','=',$this->tbName.'.movie_id')
            ->select($this->tbName.'.*','medias.name as media_name','url','pre_url')
            ->first();
        return $lessonInfo;
    }

    public function getLessonListByClassId($classId)
    {
        $lessonesInfo = DB::table($this->tbName)
            ->where($this->tbName.'.class_id', $classId)
            ->leftJoin('medias','medias.id','=',$this->tbName.'.movie_id')
            ->select($this->tbName.'.*','medias.name as media_name','url','pre_url')
            ->get();
        return $lessonesInfo;
    }

    public function deleteLesson($lessonId)
    {
        DB::table($this->tbName)->where('id', $lessonId)->delete();
    }

    public function updateLessonMovie($lessonId, $movieId)
    {
        DB::table($this->tbName)->where('id', $lessonId)->update([
            'movie_id' => $movieId
        ]);
    }

    public function getLessonOwner($lessonId)
    {
        $owner = DB::table($this->tbName)
            ->where('lessons.id', $lessonId)
            ->join('classes', 'classes.id', '=', 'lessons.class_id')
            ->value('classes.teacher_id');
        return $owner;
    }

    public function getLessonClassId($lessonId)
    {
        $classId = DB::table($this->tbName)->where('id', $lessonId)->value('class_id');
        return $classId;
    }
}