<?php

namespace App\Http\Middleware;

use App\Services\TokenService;
use App\Tools\Code;
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

            if ($request->user->role !== 'teacher'){
                return response()->json(Code::NO_PERMISSION);
            }
            return $next($request);

    }
}
