<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // เช็คว่าใน Session มีข้อมูล 'customer' ที่เราใส่ไว้ตอน Login สำเร็จหรือไม่
        if (!$request->session()->has('customer')) {

            // ถ้าไม่มี (แปลว่ายังไม่ได้ Login หรือ Session หมดอายุ)
            // ระบบจะจำ URL ที่เขากำลังจะเข้าไว้ (Intended URL) เช่น ลิงก์ดาวน์โหลดจาก PDF
            // แล้วพาไปหน้า Login ของลูกค้า
            return redirect()->route('customer.login')->with('error', 'กรุณาเข้าสู่ระบบก่อนดาวน์โหลดไฟล์');
        }

        return $next($request);
    }
}
