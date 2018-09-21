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
                'message' => '修改错误,请检查信息是否确实有改动',
                'data' => ''
            ]);
        }
    }
    public function resetPassword(Request $request)
    {
        $rules = [
            'old_password' => 'required',
            'new_password' => 'required|string|min:6|max:20'
        ];
        $validator = ValidationHelper::validateCheck($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'code' => 1001,
                'message' => $validator->errors()
            ]);
        }

        $res = $this->userService->resetPassword($request->user->id, $request->old_password, $request->new_password);
        if (!$res)
            return response()->json([
                'code' => 306,
                'message' => "原密码错误"
            ]);
        else
            return response()->json([
                'code' => 0,
                'message' => "密码重置成功"
            ]);
    }
    public function addCoin(Request $request){
        $rules = [
            'coin' => 'required|integer'
        ];
        $validator = ValidationHelper::validateCheck($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'code' => 300,
                'message' => $validator->errors()
            ]);
        }
        $userInfo = ValidationHelper::getInputData($request, $rules);
        $status = $this->userService->addCoin($request->user->id,$userInfo['coin']);
        if ($status == -1){
            return response()->json([
                'code' => 307,
                'message' => "输入钱数必须大于0"
            ]);
        }elseif ($status == -2){
            return response()->json([
                'code' => 308,
                'message' => "充值失败，若已扣费，请联系管理员"
            ]);
        }else{
            return response()->json([
                'code' => 0,
                'message' => "充值成功",
                'data' => [
                    'coin' => $status
                ]
            ]);
        }
    }
    public function delCoin(Request $request){
        $rules = [
            'coin' => 'required|integer'
        ];
        $validator = ValidationHelper::validateCheck($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'code' => 300,
                'message' => $validator->errors()
            ]);
        }
        $userInfo = ValidationHelper::getInputData($request, $rules);
        $status = $this->userService->delCoin($request->user->id,$userInfo['coin']);
        if ($status == -1){
            return response()->json([
                'code' => 307,
                'message' => "输入钱数必须大于0"
            ]);
        }elseif ($status == -2){
            return response()->json([
                'code' => 308,
                'message' => "扣费失败"
            ]);
        }elseif ($status == -3){
            return response()->json([
                'code' => 309,
                'message' => "扣费失败，余额不足"
            ]);
        }else{
            return response()->json([
                'code' => 0,
                'message' => "扣费成功",
                'data' => [
                    'coin' => $status
                ]
            ]);
        }
    }
//    public function forgotPassword(Request $request)
//    {
//        $rules = [
//            'mobile' => 'required',
//            'captcha' => 'required',
//            'new_password' => 'required|string|min:6|max:20'
//        ];
//        $validator = ValidationHelper::validateCheck($request->all(), $rules);
//
//        if ($validator->fails()) {
//            return response()->json([
//                'code' => 1001,
//                'message' => $validator->errors()
//            ]);
//        }
//        if (!$this->userService->checkCaptcha($request->mobile, $request->captcha)) {
//            return response()->json([
//                'code' => 1007,
//                'message' => '验证码错误'
//            ]);
//        }
//        $userId = $this->userService->getUserId($request->mobile);
//        $this->userService->forgotPassword($userId, $request->new_password);
//        return response()->json([
//            'code' => 1000,
//            'message' => '密码重置成功'
//        ]);
//    }



}