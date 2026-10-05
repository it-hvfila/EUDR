<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;

class SupplierDocumentController extends Controller
{
    /**
     * =========================================================
     * PAGE 1
     * Supplier List
     * =========================================================
     */
    public function suppliers(Request $request)
    {
        $suppliers = DB::connection('mysql2')
            ->table('suppliers')
            ->select(
                'id',
                'supplier_code',
                'supplier_name'
            )
            ->orderBy('supplier_name');

        if ($request->ajax()) {

            return DataTables::of($suppliers)

                ->addIndexColumn()

                ->addColumn('document_count', function ($supplier) {

                    return DB::connection('mysql2')
                        ->table('supplier_documents')
                        ->where('supplier_id', $supplier->id)
                        ->count();

                })

                ->addColumn('Detail', function ($supplier) {

                    return '
                        <a href="' . route(
                            'supplier_docs.list',
                            $supplier->id
                        ) . '"
                        class="btn btn-sm btn-primary text-white">
                            <i class="fa fa-folder-open"></i>
                            View Documents
                        </a>
                    ';

                })

                ->rawColumns(['Detail'])

                ->make(true);
        }

        return view('supplier_documents');
    }


    /**
     * =========================================================
     * PAGE 2
     * Documents ของ Supplier
     * =========================================================
     */
    public function documentList(Request $request, $supplier_id)
    {
        $supplier = DB::connection('mysql2')
            ->table('suppliers')
            ->where('id', $supplier_id)
            ->first();

        if (!$supplier) {
            abort(404, 'Supplier not found');
        }

        $documents = DB::connection('mysql2')
            ->table('supplier_documents as sd')

            ->leftJoin(
                'document_categories as dc',
                'sd.category_id',
                '=',
                'dc.id'
            )

            ->select(
                'sd.*',
                'dc.category_name',
                'dc.report_section'
            )

            ->where('sd.supplier_id', $supplier_id)

            ->orderBy('sd.created_at', 'desc');


        if ($request->ajax()) {

            return DataTables::of($documents)

                ->addIndexColumn()

                ->addColumn('Detail', function ($record) {

                    return '

                        <div class="d-flex gap-2">

                            <a href="' . route(
                                'supplier_docs.view',
                                $record->token
                            ) . '"
                            class="btn btn-sm btn-primary text-white"
                            target="_blank">

                                <i class="fa fa-eye"></i>

                            </a>


                            <a href="' . route(
                                'supplier_docs.download',
                                $record->token
                            ) . '"
                            class="btn btn-sm btn-success text-white">

                                <i class="bi bi-download"></i>

                            </a>


                            <button
                                class="btn btn-sm btn-warning edit-document"
                                data-id="' . $record->id . '">

                                <i class="fa fa-edit"></i>

                            </button>


                            <button
                                class="btn btn-sm btn-danger delete-document"
                                data-id="' . $record->id . '">

                                <i class="fa fa-trash"></i>

                            </button>

                        </div>

                    ';

                })

                ->rawColumns(['Detail'])

                ->make(true);
        }


        // Category
        $categories = DB::connection('mysql2')
            ->table('document_categories')
            ->orderBy('report_section')
            ->orderBy('category_name')
            ->get();


        return view('supplier_document_list', [

            'supplier' => $supplier,

            'categories' => $categories,

            'supplier_id' => $supplier_id

        ]);
    }


    /**
     * =========================================================
     * Get Document
     * =========================================================
     */
    public function getDocument($id)
    {
        $doc = DB::connection('mysql2')
            ->table('supplier_documents')
            ->where('id', $id)
            ->first();

        if (!$doc) {

            return response()->json([
                'success' => false,
                'message' => 'Document not found'
            ]);

        }

        return response()->json($doc);
    }


    /**
     * =========================================================
     * Save / Update Document
     * =========================================================
     */
    public function saveDocument(Request $request)
    {
        $request->validate([

            'doc_name' => 'required|string',

            'category_id' => 'required|integer',

            'supplier_id' => 'required|integer',

            'file_input' => 'nullable|file|max:20480',

            'description' => 'nullable|string',

        ]);


        if ($request->doc_id) {
            \App\Services\PrivateReportFiles::assertUnlinked('supplier_document_id', [$request->doc_id]);
        }

        $data = [

            'doc_name' => $request->doc_name,

            'category_id' => $request->category_id,

            'supplier_id' => $request->supplier_id,

            'description' => $request->description,

            'upload_by' => session('users.name') ?? 'system',

        ];


        /**
         * Upload File
         */
        if ($request->hasFile('file_input')) {

            $file = $request->file('file_input');

            $ext = $file->getClientOriginalExtension();


            $filename = (string) \Illuminate\Support\Str::uuid().'.'.$file->extension();


            $uploadPath =
                storage_path('app/private/uploads/supplier_doc');


            if (!file_exists($uploadPath)) {

                mkdir(
                    $uploadPath,
                    0750,
                    true
                );

            }


            $file->move(
                $uploadPath,
                $filename
            );


            $data['file_path'] =
                'uploads/supplier_doc/' . $filename;
        }


        /**
         * Update
         */
        if ($request->doc_id) {

            DB::connection('mysql2')
                ->table('supplier_documents')
                ->where('id', $request->doc_id)
                ->update($data);

        }

        /**
         * Insert
         */
        else {

            $data['token'] =
                substr(md5(uniqid()), 0, 10);

            $data['created_at'] = now();

            DB::connection('mysql2')
                ->table('supplier_documents')
                ->insert($data);

        }


        return response()->json([
            'success' => true
        ]);
    }


    /**
     * =========================================================
     * View File
     * =========================================================
     */
    public function view($token)
    {
        $doc = DB::connection('mysql2')
            ->table('supplier_documents')
            ->where('token', $token)
            ->first();


        if (!$doc) {
            abort(404, 'File not found');
        }


        $filePath =
            \App\Services\PrivateReportFiles::existing($doc->file_path);


        if (!file_exists($filePath)) {
            abort(404, 'File not found on server');
        }


        return response()->file($filePath);
    }


    /**
     * =========================================================
     * Download File
     * =========================================================
     */
    public function download($token)
    {
        $doc = DB::connection('mysql2')
            ->table('supplier_documents')
            ->where('token', $token)
            ->first();


        if (!$doc) {
            abort(404, 'File not found');
        }


        $filePath =
            \App\Services\PrivateReportFiles::existing($doc->file_path);


        if (!file_exists($filePath)) {
            abort(404, 'File not found on server');
        }


        return response()->download(

            $filePath,

            $doc->doc_name
            . '.'
            . pathinfo(
                $doc->file_path,
                PATHINFO_EXTENSION
            )

        );
    }


    /**
     * =========================================================
     * Delete
     * =========================================================
     */
    public function destroy($id)
    {
        \App\Services\PrivateReportFiles::assertUnlinked('supplier_document_id', [$id]);
        $doc = DB::connection('mysql2')
            ->table('supplier_documents')
            ->where('id', $id)
            ->first();


        if ($doc) {

            if ($doc->file_path) {

                $filePath =
                    \App\Services\PrivateReportFiles::path($doc->file_path);

                if (file_exists($filePath)) {
                    unlink($filePath);
                }

            }


            DB::connection('mysql2')
                ->table('supplier_documents')
                ->where('id', $id)
                ->delete();

        }


        return response()->json([
            'success' => true
        ]);
    }
}