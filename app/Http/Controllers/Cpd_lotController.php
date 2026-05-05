<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use Illuminate\Support\Facades\Storage;
use App\Services\TelegramNotifyService;

class Cpd_lotController extends Controller
{
    protected $telegramNotify;

    public function __construct(TelegramNotifyService $telegramNotify)
    {
        $this->telegramNotify = $telegramNotify;
    }

    /**
     * หน้า list CPD ของ Lot
     */
    public function index(Request $request, $lot_id)
    {
        $cpd_lot = DB::connection('mysql2')
            ->table('compound_lots_links')
            ->select(
                'id',
                'lot_cpd_no',
                'remak',
                'created_at',
                'created_by'
            )
            ->where('lot_id', $lot_id);

        if ($request->ajax()) {
            return DataTables::of($cpd_lot)
                ->addIndexColumn()
                ->addColumn('Detail', function ($record) {
                    return '
                        <button class="btn btn-sm btn-warning edit-cpd-lot"
                            data-id="' . $record->id . '">
                            <i class="fa fa-edit"></i>
                        </button>

                        <button class="btn btn-sm btn-danger delete-cpd-lot"
                            data-id="' . $record->id . '">
                            <i class="fa fa-trash"></i>
                        </button>
                        <a class="btn btn-sm btn-success"
                        href="' . url('fg-cpd/' . $record->id) . '">
                            <i class="bi bi-diagram-3"></i> FG Lot
                        </a>
                        ';
                })
                ->rawColumns(['Detail'])
                ->make(true);
        }

        // header: Lot + Supplier
        $lot = DB::connection('mysql2')
            ->table('lots')
            ->join('suppliers', 'lots.supplier_id', '=', 'suppliers.id')
            ->select(
                'lots.id',
                'lots.lot_number',
                'suppliers.supplier_name as supplier_name'
            )
            ->where('lots.id', $lot_id)
            ->first();

        return view('cpd_lot', [
            'lot_id'        => $lot_id,
            'lot_number'    => $lot?->lot_number ?? 'Unknown Lot',
            'supplier_name' => $lot?->supplier_name ?? 'Unknown Supplier',
        ]);
    }

    /**
     * บันทึกการ map CPD → Lot
     */
    public function store(Request $request)
    {
        $request->validate([
            'lot_id'     => 'required|integer',
            'lot_cpd_no' => 'required|string|max:50',
            'remak'      => 'nullable|string|max:255',
        ]);

        $exists = DB::connection('mysql2')
            ->table('compound_lots_links')
            ->where('lot_id', $request->lot_id)
            ->where('lot_cpd_no', $request->lot_cpd_no)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'CPD นี้ถูก map กับ Lot นี้แล้ว'
            ]);
        }

        DB::connection('mysql2')
            ->table('compound_lots_links')
            ->insert([
                'lot_id'     => $request->lot_id,
                'lot_cpd_no' => $request->lot_cpd_no,
                'remak'      => $request->remak,
                'created_by' => session('users.username'),
                'created_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'บันทึกข้อมูลสำเร็จ'
        ]);
    }

    /**
     * ดึงข้อมูล CPD 1 รายการ (ใช้ตอน edit)
     */
    public function show($id)
    {
        $cpd = DB::connection('mysql2')
            ->table('compound_lots_links')
            ->where('id', $id)
            ->first();

        if (!$cpd) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบข้อมูล'
            ]);
        }

        return response()->json($cpd);
    }

    /**
     * แก้ไข CPD
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'lot_cpd_no' => 'required|string|max:50',
            'remak'      => 'nullable|string|max:255',
        ]);

        DB::connection('mysql2')
            ->table('compound_lots_links')
            ->where('id', $id)
            ->update([
                'lot_cpd_no' => $request->lot_cpd_no,
                'remak'      => $request->remak,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'แก้ไขข้อมูลสำเร็จ'
        ]);
    }

    /**
     * ลบ CPD
     */
    public function destroy($id)
    {
        DB::connection('mysql2')
            ->table('compound_lots_links')
            ->where('id', $id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'ลบข้อมูลสำเร็จ'
        ]);
    }
}
