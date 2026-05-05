<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;

class SupplierDocumentController extends Controller
{
    public function getDocument($id)
    {
        $doc = DB::connection('mysql2')->table('supplier_documents')->where('id', $id)->first();
        if (!$doc) {
            return response()->json(['success' => false, 'message' => 'Document not found']);
        }
        return response()->json($doc);
    }
    public function index(Request $request)
    {
        $documents = DB::connection('mysql2')
            ->table('supplier_documents')
            ->orderBy('created_at', 'desc');

        if ($request->ajax()) {
            return DataTables::of($documents)
                ->addIndexColumn()
                ->addColumn('Detail', function ($record) {
                    return '
    <div class="d-flex gap-2">
        <a href="' . url('supplier_docs/view/' . $record->token) . '"
           class="btn btn-sm btn-primary text-white" target="_blank">
            <i class="fa fa-eye"></i>
        </a>
        <a href="' . url('supplier_docs/download/' . $record->token) . '"
           class="btn btn-sm btn-success text-white">
            <i class="bi bi-download"></i>
        </a>
        <button class="btn btn-sm btn-warning edit-document" data-id="' . $record->id . '">
            <i class="fa fa-edit"></i>
        </button>
        <button class="btn btn-sm btn-danger delete-document" data-id="' . $record->id . '">
            <i class="fa fa-trash"></i>
        </button>
    </div>';
                })
                ->rawColumns(['Detail'])
                ->make(true);
        }

        return view('supplier_documents');
    }

    public function saveDocument(Request $request)
    {
        $request->validate([
            'doc_name' => 'required|string',
            'file_input' => 'nullable|file|max:10240',
            'description' => 'nullable|string',
        ]);

        $data = [
            'doc_name' => $request->doc_name,
            'description' => $request->description,
            'upload_by' => session('users.name') ?? 'system',
        ];

        // 🔹 ถ้ามีไฟล์ใหม่
        if ($request->hasFile('file_input')) {
            $file = $request->file('file_input');
            $ext = $file->getClientOriginalExtension();

            // ตั้งชื่อไฟล์ให้ไม่ซ้ำ
            $filename = $request->doc_name . '_' . now()->format('Ymd_His') . '_' . substr(md5(uniqid()), 0, 6) . '.' . $ext;

            // 🔹 สร้างโฟลเดอร์ย่อย: uploads/supplier_doc/
            $uploadPath = public_path('uploads/supplier_doc');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // 🔹 ย้ายไฟล์ไปเก็บในโฟลเดอร์ย่อย
            $file->move($uploadPath, $filename);

            // 🔹 บันทึก path ใน DB (ใช้ path แบบ relative)
            $data['file_path'] = 'uploads/supplier_doc/' . $filename;
        }

        // 🔹 แยกเคส แก้ไข / เพิ่มใหม่
        if ($request->doc_id) {
            DB::connection('mysql2')->table('supplier_documents')
                ->where('id', $request->doc_id)
                ->update($data);
        } else {
            $data['token'] = substr(md5(uniqid()), 0, 10);
            $data['created_at'] = now();
            DB::connection('mysql2')->table('supplier_documents')->insert($data);
        }

        return response()->json(['success' => true]);
    }

    public function view($token)
    {
        $doc = DB::connection('mysql2')->table('supplier_documents')->where('token', $token)->first();
        if (!$doc) abort(404, 'File not found');
        $filePath = public_path($doc->file_path);
        if (!file_exists($filePath)) abort(404, 'File not found on server');

        return response()->file($filePath);
    }

    public function download($token)
    {
        $doc = DB::connection('mysql2')->table('supplier_documents')->where('token', $token)->first();
        if (!$doc) abort(404, 'File not found');
        $filePath = public_path($doc->file_path);
        if (!file_exists($filePath)) abort(404, 'File not found on server');

        return response()->download($filePath, $doc->doc_name . '.' . pathinfo($doc->file_path, PATHINFO_EXTENSION));
    }

    public function destroy($id)
    {
        $doc = DB::connection('mysql2')->table('supplier_documents')->where('id', $id)->first();
        if ($doc) {
            $filePath = public_path($doc->file_path);
            if (file_exists($filePath)) unlink($filePath);
            DB::connection('mysql2')->table('supplier_documents')->where('id', $id)->delete();
        }
        return response()->json(['success' => true]);
    }
}
