<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function showLoginForm()
    {
        // ตรวจสอบว่าถ้า Login อยู่แล้ว ให้ข้ามไปหน้า download-list เลย
        if (session()->has('customer')) {
            return redirect()->route('customer.dashboard');
        }

        return view('login_customer.login'); // ชื่อไฟล์ blade ที่คุณสร้าง (resources/views/customer/login.blade.php)
    }

    public function login(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        // 1. ดึงข้อมูลลูกค้าจากฐานข้อมูล (mysql2)
        $customer = DB::connection('mysql2')
            ->table('customers')
            ->where('username', $username)
            ->first();

        // 2. ตรวจสอบว่ามี User นี้จริงไหม และสถานะ Active หรือไม่
        if (!$customer || $customer->is_active == 0) {
            return response()->json(['status' => 'error', 'message' => 'Username นี้ไม่มีในระบบ หรือถูกระงับการใช้งาน']);
        }

        // 3. ตรวจสอบวันหมดอายุ (expired_at)
        if (now()->greaterThan($customer->expired_at)) {
            return response()->json(['status' => 'error', 'message' => 'รหัสผ่านของคุณหมดอายุแล้ว (ใช้งานได้ 15-30 วัน)']);
        }

        // 4. ตรวจสอบ Password  Hash::check
        if (!Hash::check($password, $customer->password)) {
            return response()->json(['status' => 'error', 'message' => 'Username หรือ Password ไม่ถูกต้อง']);
        }

        // --- ถ้าผ่านเงื่อนไขทั้งหมดด้านบน แปลว่า Login สำเร็จ ---

        // 5. จัดรูปแบบชื่อลูกค้า (ใช้ ucwords กรณีมีชื่อ-นามสกุล)
        $formattedName = ucwords(strtolower($customer->customer_name));

        // 6. เก็บข้อมูลลง Session
        $request->session()->put('customer', [
            'customer_id' => $customer->id,
            'name'        => $formattedName,
            'logged_in_at' => now(),
        ]);

        // 7. อัปเดตเวลาเข้าใช้งานล่าสุด
        DB::connection('mysql2')
            ->table('customers')
            ->where('id', $customer->id)
            ->update(['last_login' => now()]);

        return response()->json(['status' => 'success', 'message' => 'เข้าสู่ระบบสำเร็จ!']);
    }

    public function getUser($id)
    {
        $doc = DB::connection('mysql2')->table('users')->where('id', $id)->first();
        if (!$doc) {
            return response()->json(['success' => false, 'message' => 'Document not found']);
        }
        return response()->json($doc);
    }

    public function logout(Request $request)
    {
        $request->session()->forget('users');
        Auth::logout();
        return redirect('/');
    }
    public function index(Request $request)
    {
        $users = DB::connection('mysql2')
            ->table('users')
            ->select('id', 'username', 'name', 'description', 'level', 'last_login');

        // 🔹 ถ้าเป็นการเรียกผ่าน AJAX (DataTables)
        if ($request->ajax()) {
            return \Yajra\DataTables\Facades\DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('Detail', function ($record) {
                    return '
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-warning edit-user" data-id="' . $record->id . '">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-user" data-id="' . $record->id . '">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>';
                })
                ->rawColumns(['Detail'])
                ->make(true);
        }

        // 🔹 ถ้าไม่ใช่ AJAX (คือเปิดหน้าเว็บปกติ)
        return view('manage_user');
    }


    public function store(Request $request)
    {
        $data = [
            'username' => $request->username,
            'name' => $request->name,
            'description' => $request->description,
            'level' => $request->level,
            'update_date' => now(),
            'create_date' => now(),
        ];

        if (!empty($request->password)) {
            $data['password'] = $request->password;
        }

        if ($request->id) {
            // ✅ update
            DB::connection('mysql2')->table('users')->where('id', $request->id)->update($data);
        } else {
            // ✅ insert
            DB::connection('mysql2')->table('users')->insert($data);
        }

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        DB::connection('mysql2')->table('users')->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }
}
