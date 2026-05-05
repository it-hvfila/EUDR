<?php

namespace App\Http\Controllers;


use App\Models\Status;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    //

    public function login(Request $request)
    {
        // Perform your login validation logic here
        $username = $request->input('username');
        $password = $request->input('password');

        $users = DB::connection('mysql2')
            ->table('users')
            ->where('username', $username)
            ->where('password', $password)
            ->first();
        if (!$users) {
            return response()->json(['status' => 'error', 'message' => 'Username หรือ Password ไม่ถูกต้อง']);
        }



        if ($users) {
            // Authentication successful, store the user's session
            $formattedName = ucfirst(strtolower($users->name));
            $request->session()->put('users', [
                'username' => $username,
                'name' => $formattedName,
                'level' => $users->level,
            ]);

            DB::connection('mysql2')
                ->table('users')
                ->where('username', $username)
                ->where('password', $password)
                ->update(['last_login' => now()]);

            return response()->json(['status' => 'success', 'message' => 'You have been logged in successfully!']);
        }

        return response()->json(['status' => 'error', 'message' => 'Username or Password ไม่ถูกต้อง']);
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
