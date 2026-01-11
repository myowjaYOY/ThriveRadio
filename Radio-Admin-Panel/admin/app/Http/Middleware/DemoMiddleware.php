<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DemoMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $exclude_uri = array(
            '/login',
        );
        // dd(Auth::user());
        if (env('DEMO_MODE') && Auth::user() && Auth::user()->email !== "demomodeoff@gmail.com"  && !$request->isMethod('get') && !$request->is($exclude_uri)) {
            if($request->ajax() || str_contains($request->getRequestUri(), '/api')){
                return response()->json(array(
                    'error' => true,
                    'message' => "This is not allowed in the Demo Version.",
                    'code' => 112
                ));
            } else {
                return redirect()->back()->withErrors(array(
                    'message' => "This is not allowed in the Demo Version.",
                ));
            }
        }

        return $next($request);
    }
}
