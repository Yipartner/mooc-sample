<?php

Route::get('/homework/stu/status/byclass','HomeworkController@getStuHomeworkByClassId')->middleware('token');
Route::get('/homework/stu/lesson/status','HomeworkController@isStuHomeworkFinish')->middleware('token');



Route::get('/homework/teacher/status/byclass','HomeworkController@getTeacherClassFinishStatus')->middleware('token');
Route::get('/homework/teacher/finishuser/bylesson','HomeworkController@getHomeworkFinishUser')->middleware('token');
Route::get('/homework/teacher/unfinishuser/bylesson','HomeworkController@getHomeworkNoFinishUser')->middleware('token');

Route::post('/homework/finish/one','HomeworkController@finishOneHomework')->middleware(['token','teacher']);
Route::post('/homework/finish/many','HomeworkController@finishManyHomework')->middleware(['token','teacher']);
Route::post('/homework/remove/one','HomeworkController@removeOneHomework')->middleware(['token','teacher']);
Route::post('/homework/remove/many','HomeworkController@removeHomework')->middleware(['token','teacher']);
