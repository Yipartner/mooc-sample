<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 18/10/3
 * Time: 下午7:37
 */


Route::group(['middleware' => 'token'], function () {
    Route::post('/note', "NoteController@createNote");
    Route::put('/note/{id}', "NoteController@updateNote");
    Route::get('/note/{id}', "NoteController@getNoteInfo");
    Route::delete('/note/{id}', "NoteController@deleteNote");
    Route::get('/notes/{id}', "NoteController@getNoteListByLesson");
    Route::get('/allnotes', "NoteController@getAllNotesList");
    Route::get('/notesbyclass/{id}', "NoteController@getNotesByClassId");
});