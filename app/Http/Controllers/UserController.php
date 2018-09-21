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
                'code' => 300,
                'message' => $validator->errors()
            ]);
        }
        $userInfo = ValidationHelper::getInputData($request, $rules);

        $userId = $this->userService->register($userInfo);
        if ($userId == -1)
            return response()->json([
                'code' => 301,
                'message' => '账号已存在'
            ]);
        else {
            $tokenStr = $this->tokenService->makeToken($userId);
            return response()->json([
                'code' => 0,
                'message' => '注册成功',
                'data' => [
                    'user_id' => $userId,
                    'token' => $tokenStr
                ]
            ]);
        }

    }
    public function login(Request $request)
    {
        $rules = [
            'login_num' => 'required',
            'password' => 'required|min:6|max:20'
        ];

        $validator = ValidationHelper::validateCheck($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'code' => 300,
                'message' => $validator->errors()
            ]);
        }
        $loginInfo = ValidationHelper::getInputData($request, $rules);

        $userId = $this->userService->login($loginInfo['login_num'], $loginInfo['password']);
        if ($userId == -1)
            return response()->json([
                'code' => 302,
                'message' => '用户不存在'
            ]);
        else if ($userId == -2)
            return response()->json([
                'code' => 303,
                'message' => '密码错误'
            ]);
        else {
            $tokenStr = $this->tokenService->makeToken($userId);
            return response()->json([
                'code' => 0,
                'message' => '登陆成功',
                'data' => [
                    'user_id' => $userId,
                    'token' => $tokenStr
                ]
            ]);
        }
    }
    public function loginForIdAndName(Request $request)
    {
        $rules = [
            'login_num' => 'required',
            'password' => 'required|min:6|max:20'
        ];

        $validator = ValidationHelper::validateCheck($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'code' => 300,
                'message' => $validator->errors()
            ]);
        }
        $loginInfo = ValidationHelper::getInputData($request, $rules);

        $userInfo = $this->userService->loginForIdAndName($loginInfo['login_num'], $loginInfo['password']);
        if ($userInfo == -1)
            return response()->json([
                'code' => 302,
                'message' => '用户不存在'
            ]);
        else if ($userInfo == -2)
            return response()->json([
                'code' => 303,
                'message' => '密码错误'
            ]);
        else {
            return response()->json([
                'code' => 0,
                'message' => '登陆成功',
                'data' => [
                    'user_id' => $userInfo['id'],
                    'name' => $userInfo['name']
                ]
            ]);
        }
    }
    public function userDetail(Request $request){
        $userInfo = $request->user;
        return response()->json([
            'code' => 0,
            'message' => '查询成功',
            'data' => $userInfo
        ]);
    }
    public function editName(Request $request){
        $rules = [
            'name' => 'required'
        ];
        $validator = ValidationHelper::validateCheck($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'code' => 300,
                'message' => $validator->errors()
            ]);
        }
        $user = ValidationHelper::getInputData($request, $rules);

        $status = $this->userService->editName($request->user->id,$user['name']);

        if ($status == 0){
            return response()->json([
                'code' => 0,
                'message' => '修改成功',
                'data' => ''
            ]);
        }else{
            return response()->json([
                'code' => 305,
                'message' => '修改错误,请稍后再试',
                'data' => ''
            ]);
        }
    }


}