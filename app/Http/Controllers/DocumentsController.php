<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use App\Services\TelegramNotifyService;

class DocumentsController extends Controller
{
    protected $telegramNotify;

    public function __construct(TelegramNotifyService $telegramNotify)
    {
        $this->telegramNotify = $telegramNotify;
    }


    public function index(Request $request)
    {
        $documents = DB::connection('mysql2')
            ->table('documents')
            ->orderBy('upload_date', 'desc');

        if ($request->ajax()) {
            return DataTables::of($documents)
                ->addIndexColumn() // ลำดับ #
                ->addColumn('Detail', function ($record) {
                    return '
                <div class="row">
                    <div class="col-md-4">
                        <a href="' . url('documents/view/' . $record->token) . '"
                           class="btn btn-light text-secondary" target="_blank">
                            <i class="fa fa-eye"></i> View
                        </a>
                    </div>
                    <div class="col-md-4 offset-md-1 mx-2">
                        <a href="' . url('documents/download/' . $record->token) . '"
                           class="btn btn-light text-secondary">
                            <i class="bi bi-download"></i> Download
                        </a>
                    </div>
                </div>';
                })
                ->rawColumns(['Detail'])
                ->make(true);
        }

        // return view แบบปกติ
        return view('documents');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'name_file'  => 'required|string',
            'file_input' => 'required|file|max:10240', // 10MB
            'description' => 'nullable|string'
        ]);

        $file = $request->file('file_input');

        if (!$file->isValid()) {
            return response()->json(['success' => false, 'message' => 'Invalid file upload'], 400);
        }

        $originalExt = $file->getClientOriginalExtension();

        // สร้างชื่อไฟล์แบบ name_file_วันที่_รหัสสุ่ม.ext
        $genCode = substr(md5(uniqid()), 0, 6); // สุ่ม 6 ตัว
        $dateCode = now()->format('Ymd_His');   // 20251030_153025
        $fileName = $request->name_file . '_' . $dateCode . '_' . $genCode . '.' . $originalExt;

        // โฟลเดอร์เก็บไฟล์ใน public/uploads
        $uploadPath = public_path('uploads');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // ย้ายไฟล์ไปเก็บใน public/uploads
        $file->move($uploadPath, $fileName);

        // ขนาดไฟล์หลัง move
        $fileSizeMB = round(filesize($uploadPath . '/' . $fileName) / 1024 / 1024, 2);
        $token = bin2hex(random_bytes(16));

        // เก็บข้อมูลลงฐานข้อมูล
        try {
            DB::connection('mysql2')->table('documents')->insert([
                'name_file'   => $request->name_file,
                'path_file'   => 'uploads/' . $fileName,
                'type_file'   => $originalExt,
                'size_mb'     => $fileSizeMB,
                'date_file'   => now(),
                'description' => $request->description,
                'upload_date' => now(),
                'month'       => now()->format('m'),
                'year'        => now()->format('Y'),
                'upload_by'   => session('users.name') ?? 'system',
                'token'        => $token,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database insert failed: ' . $e->getMessage()
            ], 500);
        }

        return response()->json(['success' => true]);
    }

    public function view($token)
    {
        // ดึงไฟล์จาก token แทน id
        $doc = DB::connection('mysql2')->table('documents')
            ->where('token', $token)
            ->first();

        if (!$doc) {
            abort(404, 'File not found');
        }

        $filePath = public_path($doc->path_file); // path_file ต้องเป็น path relative ใน public เช่น 'uploads/xxx.pdf'

        if (!file_exists($filePath)) {
            abort(404, 'File not found on server');
        }

        return response()->file($filePath); // Browser จะเปิด PDF แสดง
    }

    public function download($token)
    {
        // ดึงไฟล์จาก token
        $doc = DB::connection('mysql2')->table('documents')
            ->where('token', $token)
            ->first();

        if (!$doc) {
            abort(404, 'File not found');
        }

        $filePath = public_path($doc->path_file); // path_file ต้องเป็น path relative ใน public เช่น 'uploads/xxx.pdf'

        if (!file_exists($filePath)) {
            abort(404, 'File not found on server');
        }

        // ดาวน์โหลดไฟล์
        return response()->download($filePath, $doc->name_file . '.' . $doc->type_file);
    }
}
