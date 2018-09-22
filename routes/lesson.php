<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 18/9/19
 * Time: 下午10:12
 */
Route::post('/lesson', "LessonController@createLesson")->middleware(['token','teacher']);
Route::put('/lesson/{id}', "LessonController@updateLesson")->middleware(['token','teacher']);
Route::get('/lesson/{id}', "LessonController@getLessonInfo")->middleware(['token']);
Route::delete('/lesson/{id}', "LessonController@deleteLesson")->middleware(['token','teacher']);
Route::get('/lessons/{id}', "LessonController@getLessonList")->middleware(['token']);
