<?php

namespace App\Http\Middleware;

use App\Services\TokenService;
use Closure;

class TeacherMiddleware
{
    private $tokenService;
    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->hasHeader('token'))
            return response()->json([
                'code' => 1010,
                'message' => '未提交token'
            ]);
        $checkRes = $this->tokenService->verifyToken($request->header('token'));
        if($checkRes == -1)
            return response()->json([
                'code' => 1011,
                'message' => '该token不存在'
            ]);
        else if($checkRes == 0)
            return response()->json([
                'code' => 1012,
                'message' => 'token已过期，请重新登录'
            ]);
        else
        {
            $checkTeacher = $this->tokenService->teacherVerify($request->header('token'));
            if ($checkTeacher == -1){
                return response()->json([
                    'code' => 1013,
                    'message' => '您没有权限访问'
                ]);
            }
            $userInfo = $this->tokenService->getUserByToken($request->header('token'));
            $request->user = $userInfo;
            return $next($request);
        }
    }
}
