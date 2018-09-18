<?php

Route::get('/homework/stu/status/byclass','HomeworkController@getStuHomeworkByClassId')->middleware('token');
Route::get('/homework/stu/lesson/status','HomeworkController@isStuHomeworkFinish')->middleware('token');



Route::get('/homework/teacher/status/byclass','HomeworkController@getTeacherClassFinishStatus')->middleware('token');
Route::get('/homework/teacher/finishuser/bylesson','HomeworkController@getHomeworkFinishUser')->middleware('token');
Route::get('/homework/teacher/unfinishuser/bylesson','HomeworkController@getHomeworkNoFinishUser')->middleware('token');

Route::post('/api/homework/finish/one','HomeworkController@finishOneHomework')->middleware('open_api');
Route::post('/api/homework/finish/many','HomeworkController@finishManyHomework')->middleware('open_api');
Route::post('/api/homework/remove/one','HomeworkController@removeOneHomework')->middleware('open_api');
Route::post('/api/homework/remove/many','HomeworkController@removeHomework')->middleware('open_api');
