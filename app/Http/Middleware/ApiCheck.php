<?php

namespace App\Http\Middleware;

use App\Tools\Code;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\DB;

class ApiCheck
{
    /**
     * Handle an incoming request.
     *作业系统开放 API中间件
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->hasHeader('access_token')){
            return response()->json(Code::NEED_ACCESS_TOKEN);
        }
        $class = DB::table('classes')
            ->where('access_token',$request->header('access_token'))
            ->first();
        if ($class->token_expired_at < Carbon::now()){
            return response()->json(Code::ACCESS_TOKEN_EXPIRED);
        }
        $request->class = $class;
        return $next($request);
    }

}
