<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class AutoLogoutIfInactive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $timeout = 2; // 2 วินาที (เปลี่ยนเป็น 900 วินาที สำหรับ 15 นาที)

        // ตรวจสอบว่ามีค่า Session หรือไม่
        if (Session::has('lastActivity')) {
            $lastActivity = Session::get('lastActivity');
            $now = Carbon::now()->timestamp;

            // Debug ค่า Session
            // dd($lastActivity, $now, $now - $lastActivity);

            if ($now - $lastActivity > $timeout) {
                // Session::flush(); // ล้าง Session
                return redirect('/logout')->withErrors(['error' => 'คุณไม่ได้ใช้งานนานเกินกำหนด ระบบออกจากระบบแล้ว']);
            }
        }

        // อัปเดตค่าล่าสุด
        Session::put('lastActivity', Carbon::now()->timestamp);

        return $next($request);
    }
}
