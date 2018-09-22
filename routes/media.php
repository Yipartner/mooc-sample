<?php
/**
 * Created by PhpStorm.
 * User: lyh
 * Date: 18/9/19
 * Time: 下午10:13
 */

Route::get('/upload/token','MediaController@makeUploadToken')->middleware('token');
Route::post('/upload/callback','MediaController@callback');
Route::post('/notify','MediaController@notify');
Route::get('/medias/{id}','MediaController@getMediaListByClassId')->middleware('token');
