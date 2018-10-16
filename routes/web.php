<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

require_once "user.php";

require_once "homework.php";

include "class.php";
include "lesson.php";
include "media.php";
include "note.php";
include "version.php";
include "rank.php";