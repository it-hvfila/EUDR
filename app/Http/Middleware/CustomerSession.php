<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CustomerSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $id = $request->session()->get('customer.customer_id');
        $customer = $id ? DB::connection('mysql2')->table('customers')->where('id', $id)->first() : null;

        if (!$customer || !$customer->is_active ||
            !hash_equals(hash('sha256', $customer->password), (string) $request->session()->get('customer.credential_version', '')) ||
            !$customer->expired_at ||
            Carbon::parse($customer->expired_at)->lessThanOrEqualTo(now())) {
            $request->session()->forget('customer');
            if ($request->isMethod('GET') && !$request->routeIs('customer.logout')) {
                // Store only a server-observed protected URL, never a client redirect parameter.
                $request->session()->put('customer_intended', $request->fullUrl());
            }
            if ($request->expectsJson()) {
                return response()->json(['message' => 'กรุณาเข้าสู่ระบบลูกค้าอีกครั้ง'], 401);
            }

            return redirect()->route('customer.login')
                ->with('error', 'กรุณาเข้าสู่ระบบ บัญชีอาจหมดอายุหรือถูกระงับการใช้งาน');
        }

        $request->session()->put('customer.name', $customer->customer_name ?? $customer->username);

        $response = $next($request);
        $response->headers->set('Cache-Control', 'no-store, private');
        return $response;
    }
}
