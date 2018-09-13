<?php

namespace App\Http\Controllers;

use App\Services\LessonService;
use App\Services\UserService;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    private $lessonService;
    private $userService;


    public function __construct(LessonService $lessonService,UserService $userService)
    {
        $this->lessonService = $lessonService;
        $this->userService = $userService;
    }



}
