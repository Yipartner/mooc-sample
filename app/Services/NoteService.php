<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 18/10/3
 * Time: 下午6:36
 */

namespace App\Services;


use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NoteService
{
    private $tbName = 'notes';

    public function createNote($noteInfo)
    {
        $time = new Carbon();
        $fullNoteInfo = array_merge($noteInfo, [
            'created_at' => $time,
            'updated_at' => $time
        ]);
        $noteId = DB::table($this->tbName)->insertGetId($fullNoteInfo);
        return $noteId;
    }

    public function updateNote($noteId, $noteInfo)
    {
        $time = new Carbon();
        $noteInfo['updated_at'] = $time;
        DB::table($this->tbName)->where('id', $noteId)->update($noteInfo);
    }

    public function getNoteInfoById($noteId)
    {
        $noteInfo = DB::table($this->tbName)->where('id', $noteId)->first();
        return $noteInfo;
    }

    public function getMyNoteListByLessonId($lessonId, $userId)
    {
        $notesInfo = DB::table($this->tbName)->where([
            ['lesson_id', "=", $lessonId],
            ['user_id', "=", $userId],
        ])->get();
        return $notesInfo;
    }

    public function getMyAllNoteList($userId)
    {
        $noteList = DB::table($this->tbName)
            ->where('user_id', $userId)
            ->orderBy('updated_at','desc')
            ->get();
        return $noteList;
    }

    public function deleteNote($noteId)
    {
        DB::table($this->tbName)->where('id', $noteId)->delete();
    }

    public function isOwner($noteId, $userId)
    {
        $res = DB::table($this->tbName)->where('id', $noteId)->value('user_id');
        return $res == $userId;
    }

}