<?php

namespace App\Http\Controllers;

use App\Services\NoteService;
use App\Tools\Code;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    private $noteService;

    public function __construct(NoteService $noteService)
    {
        $this->noteService = $noteService;
    }

    private $noteRules = [
        'lesson_id' => 'required',
        'minute' => 'required',
        'content' => 'required',
    ];

    public function createNote(Request $request)
    {
        $userInfo = $request->user;
        $rules = $this->noteRules;
        $res = ValidationHelper::validateCheck($request->all(), $rules);
        if ($res->fails())
            return response()->json([
                'code' => 201,
                'message' => $res->errors()
                ]);
        $noteInfo = ValidationHelper::getInputData($request, $rules);
        $noteFullInfo = array_merge($noteInfo,[
            'user_id' => $userInfo->id
        ]);
        $noteId = $this->noteService->createNote($noteFullInfo);
        return response()->json([
            'code' => 0,
            'message' => 'create note success',
            'data' => [
                'note_id' => $noteId
            ]
        ]);
    }

    public function updateNote($id, Request $request)
    {
        $userInfo = $request->user;
        $rules = $this->noteRules;
        if(!$this->noteService->isOwner($id,$userInfo->id))
            return response()->json(Code::NO_PERMISSION);
        $res = ValidationHelper::validateCheck($request->all(), $rules);
        if($res->fails())
            return response()->json([
                'code' => 201,
                'message' => $res->errors()
            ]);
        $noteInfo = ValidationHelper::getInputData($request, $rules);
        $noteFullInfo = array_merge($noteInfo,[
            'user_id' => $userInfo->id
        ]);        $this->noteService->updateNote($id,$noteFullInfo);
        return response()->json(Code::SUCCESS);
    }

    public function getNoteInfo($id, Request $request)
    {
        $NoteInfo = $this->noteService->getNoteInfoById($id);
        return response()->json([
            'code' => 0,
            'message' => 'get Note info success',
            'data' => $NoteInfo
        ]);
    }

    public function deleteNote($id, Request $request)
    {
        $userInfo = $request->user;
        if(!$this->noteService->isOwner($id,$userInfo->id))
            return response()->json(Code::NO_PERMISSION);
        $this->noteService->deleteNote($id);
        return response()->json(Code::SUCCESS);
    }

    public function getNoteList($id,Request $request)
    {
        $userInfo = $request->user;
        $NoteList = $this->noteService->getMyNoteListByLessonId($id,$userInfo->id);
        return response()->json([
            'code' => 0,
            'message' => 'get class list success',
            'data' => $NoteList
        ]);
    }
}
