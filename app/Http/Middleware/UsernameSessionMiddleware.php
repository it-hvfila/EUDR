<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Redirect;

class UsernameSessionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->session()->has('users');
        if ($user == true) {
            return $next($request);
        }

        return redirect('/')->withErrors(['middelwareLogin' => 'กรุณาเข้าสู่ระบบก่อน!!'])
            ->withInput()
            ->with('error', 'Invalid session');
    }
}
