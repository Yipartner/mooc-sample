<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 18/9/19
 * Time: 下午10:12
 */

Route::post('/class', "ClassController@createClass")->middleware(['token','teacher']);
Route::put('/class/{id}', "ClassController@updateClass")->middleware(['token','teacher']);
Route::get('/class/{id}', "ClassController@getClassInfo")->middleware(['token']);
Route::get('/classes', "ClassController@getAllClassList")->middleware(['token']);
Route::get('/myclass', "ClassController@getMyClassList")->middleware(['token','teacher']);
Route::get('/lessonby/{id}', "ClassController@getClassLessons")->middleware(['token']);
Route::post('/buylesson/{id}', "ClassController@buyClass")->middleware(['token']);
