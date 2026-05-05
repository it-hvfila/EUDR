<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use App\Services\TelegramNotifyService;

class SupplierController extends Controller
{
    protected $telegramNotify;

    public function __construct(TelegramNotifyService $telegramNotify)
    {
        $this->telegramNotify = $telegramNotify;
    }


    public function index(Request $request)
    {
        $suppliers = DB::connection('mysql2')->table('suppliers');

        if ($request->ajax()) {
            return DataTables::of($suppliers)
                ->addIndexColumn()
                ->addColumn('Detail', function ($record) {
                    return '
                <a href="' . url('supplier/' . $record->id . '/lots') . '" class="btn btn-sm btn-primary">
                    View Lots
                </a>
                <button class="btn btn-sm btn-danger btn-delete" data-id="' . $record->id . '">
                    Delete
                </button>
                ';
                })
                ->rawColumns(['Detail'])
                ->make(true);
        }

        return view('supplier');
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_code' => 'required|string|max:255',
            'supplier_name' => 'required|string|max:255',
            'address'       => 'nullable|string',
            'contact_name'  => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:50',
        ]);

        try {
            DB::connection('mysql2')->table('suppliers')->insert([
                'supplier_code' => $request->supplier_code,
                'supplier_name' => $request->supplier_name,
                'address'       => $request->address,
                'contact_name'  => $request->contact_name,
                'contact_phone' => $request->contact_phone,
                'created_at'    => now(),
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {

        try {
            DB::connection('mysql2')->table('suppliers')->where('id', $id)->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
