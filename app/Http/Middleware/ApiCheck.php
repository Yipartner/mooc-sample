<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\DB;

class ApiCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->hasHeader('access_token')){
            return response()->json([
                'code' => 101,
                'message' => 'need access_token!'
            ]);
        }
        $class = DB::table('classes')
            ->where('access_token',$request->header('access_token'))
            ->first();
        if ($class->token_expired_at < Carbon::now()){
            return response()->json([
                'code' => 102,
                'message' => 'access_token expired'
            ]);
        }
        $request->class = $class;
        return $next($request);
    }

}
