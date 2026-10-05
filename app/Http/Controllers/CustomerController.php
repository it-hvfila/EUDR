<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class CustomerController extends Controller
{
    public function showLoginForm()
    {
        if (session()->has('customer')) {
            return redirect()->route('customer.dashboard');
        }

        return view('login_customer.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string|max:100',
            'password' => 'required|string|max:255',
        ]);

        // Use an IP limit as well as an account limit to prevent username rotation.
        $keys = [
            'customer-login-ip:'.$request->ip(),
            'customer-login-account:'.hash('sha256', mb_strtolower($credentials['username'])),
        ];
        foreach ($keys as $key) {
            if (RateLimiter::tooManyAttempts($key, 10)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ลองเข้าสู่ระบบบ่อยเกินไป กรุณารอสักครู่แล้วลองใหม่',
                ], 429)->header('Retry-After', RateLimiter::availableIn($key));
            }
        }
        foreach ($keys as $key) {
            RateLimiter::hit($key, 60);
        }

        $customer = DB::connection('mysql2')->table('customers')
            ->where('username', $credentials['username'])->first();

        // Run a hash check even when the account does not exist.
        $hash = $customer->password ?? '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
        $validPassword = Hash::check($credentials['password'], $hash);
        if (!$customer || !$validPassword || !$customer->is_active ||
            !$customer->expired_at || Carbon::parse($customer->expired_at)->lessThanOrEqualTo(now())) {
            return response()->json([
                'status' => 'error',
                'message' => 'ไม่สามารถเข้าสู่ระบบได้ กรุณาตรวจสอบข้อมูลหรือติดต่อผู้ดูแล',
            ], 401);
        }

        DB::connection('mysql2')->table('customers')->where('id', $customer->id)
            ->update(['last_login' => now()]);
        RateLimiter::clear($keys[1]);
        $request->session()->regenerate();
        $request->session()->put('customer', [
            'customer_id' => $customer->id,
            'name' => $customer->customer_name ?? $customer->username,
            'logged_in_at' => now()->toDateTimeString(),
            'credential_version' => hash('sha256', $customer->password),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'เข้าสู่ระบบสำเร็จ!',
            'redirect_url' => $request->session()->pull('customer_intended', route('customer.dashboard')),
        ]);
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['customer', 'customer_intended']);
        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return redirect()->route('customer.login');
    }

    public function index(Request $request)
    {
        $reports = DB::connection('mysql2')->table('customer_reports')
            ->where('customer_id', $request->session()->get('customer.customer_id'))
            ->where('is_active', 1)
            ->where('expired_at', '>', now())
            ->orderByDesc('created_at')->paginate(15);

        return view('login_customer.dashboard', compact('reports'));
    }
    private function permittedReport(Request $request, string $token)
    {
        $report = DB::connection('mysql2')->table('customer_reports')
            ->where('token', $token)->where('customer_id', $request->session()->get('customer.customer_id'))
            ->where('is_active', 1)->where('expired_at', '>', now())->first();
        abort_unless($report, 404, 'Report unavailable');
        return $report;
    }

    public function reportFiles(Request $request, string $report)
    {
        $report = $this->permittedReport($request, $report);
        $data = app(\App\Services\CustomerReports::class)->viewData($report);
        unset($data['reportAccount']);

        return view('Invoice', $data + ['customerPortal' => true]);
    }

    public function download(Request $request, string $report, string $file)
    {
        $report = $this->permittedReport($request, $report);
        $db = DB::connection('mysql2');
        $entry = $db->table('customer_report_files')->where('report_id', $report->id)->where('token', $file)->first();
        abort_unless($entry, 404);
        foreach (['lot_file_id' => 'lot_files', 'supplier_document_id' => 'supplier_documents', 'company_document_id' => 'company_documents'] as $column => $table) {
            if ($entry->$column) {
                $source = $db->table($table)->where('id', $entry->$column)->first();
                break;
            }
        }
        abort_unless(isset($source) && $source, 404);
        $path = \App\Services\PrivateReportFiles::existing($source->file_path);
        $name = $source->file_name ?? ($source->doc_name.'.'.pathinfo($path, PATHINFO_EXTENSION));
        $response = response()->download($path, basename(str_replace('\\', '/', $name)));
        $response->headers->set('Cache-Control', 'no-store, private');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        // Records an authorized download response, not proof the transfer completed.
        $db->table('log_downloads')->insert([
            'customer_id' => $report->customer_id, 'report_id' => $report->id, 'report_file_id' => $entry->id,
            'topic_name' => mb_substr($name, 0, 255), 'file_token' => $entry->token,
            'ip_address' => $request->ip(), 'user_agent' => mb_substr($request->userAgent() ?? '', 0, 1000),
        ]);
        return $response;
    }

}
