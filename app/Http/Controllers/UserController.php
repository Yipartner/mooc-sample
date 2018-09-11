<?php
/**
 * Created by PhpStorm.
 * User: maoqiu
 * Date: 2018/9/11
 * Time: 下午5:33
 */

namespace App\Http\Controllers;


use App\Services\TokenService;
use App\Services\UserService;
use App\Tools\ValidationHelper;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $userService;
    private $tokenService;

    public function __construct(UserService $userService, TokenService $tokenService)
    {
        $this->userService = $userService;
        $this->tokenService = $tokenService;
    }
    public function register(Request $request)
    {
        $rules = [
            'login_num' => 'required',
            'password' => 'required|min:6|max:20',
            'is_teacher' => ''
        ];

        $validator = ValidationHelper::validateCheck($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'code' => 1001,
                'message' => $validator->errors()
            ]);
        }
        $userInfo = ValidationHelper::getInputData($request, $rules);

        $userId = $this->userService->register($userInfo);
        if ($userId == -1)
            return response()->json([
                'code' => 1003,
                'message' => '账号已存在'
            ]);
        else {
            $tokenStr = $this->tokenService->makeToken($userId);
            return response()->json([
                'code' => 1000,
                'message' => '注册成功',
                'data' => [
                    'user_id' => $userId,
                    'token' => $tokenStr
                ]
            ]);
        }

    }

}