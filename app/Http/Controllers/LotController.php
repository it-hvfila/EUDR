<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use Illuminate\Support\Facades\Storage;
use App\Services\TelegramNotifyService;

class LotController extends Controller
{
    protected $telegramNotify;

    public function __construct(TelegramNotifyService $telegramNotify)
    {
        $this->telegramNotify = $telegramNotify;
    }

    public function index(Request $request, $supplier_id)
    {
        $lots = DB::connection('mysql2')
            ->table('lots')
            ->select('id', 'lot_number', 'lot_date', 'description')
            ->where('supplier_id', $supplier_id);

        if ($request->ajax()) {
            return DataTables::of($lots)
                ->addIndexColumn()
                ->addColumn('Detail', function ($record) {
                    return '
        <button class="btn btn-sm btn-warning edit-lot" data-id="' . $record->id . '">
            <i class="fa fa-edit"></i>
        </button>

        <button class="btn btn-sm btn-danger delete-lot" data-id="' . $record->id . '">
            <i class="fa fa-trash"></i>
        </button>

        <button class="btn btn-sm btn-primary" id="file_input_modal" data-id="' . $record->id . '">
            <i class="bi bi-folder-plus"></i>
        </button>

            <a class="btn btn-sm btn-success"
        href="' . url('cpd-lots/' . $record->id) . '">
            <i class="bi bi-diagram-3"></i> CPD Lot
        </a>
            ';
                })

                ->rawColumns(['Detail'])
                ->make(true);
        }

        // ดึงชื่อ supplier
        $supplier = DB::connection('mysql2')
            ->table('suppliers')
            ->select('supplier_name', 'supplier_code')
            ->where('id', $supplier_id)
            ->first();

        return view('lots', [
            'supplier_id' => $supplier_id,
            'supplier_name' => $supplier ? $supplier->supplier_name : 'Unknown Supplier',
            'supplier_code' => $supplier ? $supplier->supplier_code : 'Unknown Supplier',
        ]);
    }
    public function show($id)
    {
        $lot = DB::connection('mysql2')->table('lots')->where('id', $id)->first();

        if (!$lot) {
            return response()->json(['success' => false, 'message' => 'Lot not found'], 404);
        }

        return response()->json([
            'lot_number' => $lot->lot_number,
            'lot_date'   => $lot->lot_date,
            'description' => $lot->description
        ]);
    }

    /**
     * ดึงไฟล์ของ Lot
     */

    public function getFiles($id)
    {
        $files = DB::connection('mysql2')
            ->table('lot_files')
            ->where('lot_id', $id)
            ->get()
            ->map(function ($file) {
                return [
                    'id' => $file->id,
                    'file_name' => $file->file_name,
                    'file_size' => $file->file_size,
                    'file_path' => $file->file_path,
                    'file_extension' => pathinfo($file->file_name, PATHINFO_EXTENSION),
                ];
            });

        return response()->json($files);
    }
    //อัปโหลดไฟล์สำหรับ Lot
    public function uploadLotFile(Request $request, $id)
    {
        $file = $request->file('file'); // fileinput async จะส่งทีละไฟล์
        if (!$file) {
            return response()->json(['success' => false, 'message' => 'No file received'], 400);
        }

        // 🔹 ดึง lot_number ของ lot นี้
        $lot = DB::connection('mysql2')->table('lots')->where('id', $id)->first();
        if (!$lot) {
            return response()->json(['success' => false, 'message' => 'Lot not found'], 404);
        }

        $lotNumber = $lot->lot_number;

        // 🔹 สร้าง folder ถ้ายังไม่มี
        $uploadPath = public_path('uploads/lots');
        if (!file_exists($uploadPath)) mkdir($uploadPath, 0777, true);

        // 🔹 ตั้งชื่อไฟล์: lot_number_YYYYMMDD_His_สุ่ม.ext
        $dateCode = now()->format('Ymd_His'); // วันที่เวลา
        $uniqueCode = substr(md5(uniqid()), 0, 6); // รหัสสุ่ม 6 ตัว
        $extension = $file->getClientOriginalExtension();
        $fileName = $lotNumber . '_' . $dateCode . '_' . $uniqueCode . '.' . $extension;

        // 🔹 ย้ายไฟล์
        $file->move($uploadPath, $fileName);

        $fileSizeMB = round(filesize($uploadPath . '/' . $fileName) / 1024 / 1024, 2);

        try {
            DB::connection('mysql2')->table('lot_files')->insert([
                'lot_id'      => $id,
                'file_name'   => $fileName,
                'file_size'   => $fileSizeMB,
                'file_path'   => 'uploads/lots/' . $fileName,
                'created_at'  => now(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        return response()->json(['success' => true]);
    }

    //ลบไฟล์ Lot
    public function deleteFile($id)
    {
        $file = DB::connection('mysql2')->table('lot_files')->where('id', $id)->first();

        if ($file) {
            $filePath = public_path($file->file_path);

            // ลบไฟล์จริงถ้ามีอยู่
            if (file_exists($filePath) && is_file($filePath)) {
                try {
                    unlink($filePath);
                } catch (\Exception $e) {
                    // ถ้าไฟล์ลบไม่ได้ log ไว้ แต่ยังลบ DB ต่อ
                    \Log::error("Failed to delete file: {$filePath}. Error: " . $e->getMessage());
                }
            }

            // ลบข้อมูลใน DB
            DB::connection('mysql2')->table('lot_files')->where('id', $id)->delete();
        }

        return response()->json(['success' => true]);
    }

    // เพิ่ม Lot
    public function store(Request $request)
    {
        $request->validate([
            'lot_number' => 'required|string|unique:mysql2.lots,lot_number',
            'lot_date'   => 'required|date',
            'description' => 'nullable|string',
            'supplier_id' => 'required|integer',
        ]);

        $id = DB::connection('mysql2')->table('lots')->insertGetId([
            'lot_number' => $request->lot_number,
            'lot_date'   => $request->lot_date,
            'description' => $request->description,
            'supplier_id' => $request->supplier_id,
            'created_at' => now(),
        ]);

        return response()->json(['success' => true, 'id' => $id]);
    }

    // แก้ไข Lot
    public function update(Request $request, $id)
    {
        $request->validate([
            'lot_number' => 'required|string|unique:mysql2.lots,lot_number,' . $id,
            'lot_date'   => 'required|date',
            'description' => 'nullable|string',
        ]);

        DB::connection('mysql2')->table('lots')->where('id', $id)->update([
            'lot_number' => $request->lot_number,
            'lot_date'   => $request->lot_date,
            'description' => $request->description,
        ]);

        return response()->json(['success' => true]);
    }

    // ลบ Lot
    public function destroy($id)
    {
        // 1️⃣ ดึงไฟล์ทั้งหมดของ lot
        $files = DB::connection('mysql2')->table('lot_files')->where('lot_id', $id)->get();

        foreach ($files as $file) {
            $filePath = public_path($file->file_path); // ตัวอย่าง: public/uploads/lots/filename.pdf
            // ลบไฟล์จริงในเซิร์ฟเวอร์ถ้ามี
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // 2️⃣ ลบข้อมูลในตาราง lot_files
        DB::connection('mysql2')->table('lot_files')->where('lot_id', $id)->delete();

        // 3️⃣ ลบข้อมูล lot
        DB::connection('mysql2')->table('lots')->where('id', $id)->delete();

        // 4️⃣ ส่งผลลัพธ์กลับ
        return response()->json([
            'success' => true,
            'message' => 'Lot and all related files have been deleted.'
        ]);
    }
}
