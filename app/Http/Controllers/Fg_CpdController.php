<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use App\Services\TelegramNotifyService;

class Fg_CpdController extends Controller
{
    protected $telegramNotify;

    public function __construct(TelegramNotifyService $telegramNotify)
    {
        $this->telegramNotify = $telegramNotify;
    }

    /**
     * หน้า list FG ของ CPD
     */
    public function index(Request $request, $cpd_id)
    {
        $fg = DB::connection('mysql2')
            ->table('compound_fg_links')
            ->select(
                'id',
                'cpd_id',
                'fg_lot_no',
                'remak',
                'created_at',
                'created_by'
            )
            ->where('cpd_id', $cpd_id);

        if ($request->ajax()) {
            return DataTables::of($fg)
                ->addIndexColumn()
                ->addColumn('Detail', function ($record) {
                    return '
                        <button class="btn btn-sm btn-warning edit-fg"
                            data-id="' . $record->id . '">
                            <i class="fa fa-edit"></i>
                        </button>

                        <button class="btn btn-sm btn-danger delete-fg"
                            data-id="' . $record->id . '">
                            <i class="fa fa-trash"></i>
                        </button>';
                })
                ->rawColumns(['Detail'])
                ->make(true);
        }

        // header: Supplier + Lot + CPD
        $header = DB::connection('mysql2')
            ->table('compound_lots_links as c')
            ->join('lots as l', 'l.id', '=', 'c.lot_id')
            ->join('suppliers as s', 'l.supplier_id', '=', 's.id')
            ->select(
                's.supplier_name',
                'l.lot_number',
                'c.lot_cpd_no'
            )
            ->where('c.id', $cpd_id)
            ->first();

        return view('fg_cpd', [
            'cpd_id'        => $cpd_id,
            'lot_number'    => $header?->lot_number ?? '-',
            'supplier_name' => $header?->supplier_name ?? '-',
            'lot_cpd_no'    => $header?->lot_cpd_no ?? '-',
        ]);
    }

    /**
     * บันทึก FG map CPD
     */
    public function store(Request $request)
    {
        $request->validate([
            'cpd_id'    => 'required|integer',
            'fg_lot_no' => 'required|string|max:50',
            'remak'     => 'nullable|string|max:255',
        ]);

        $exists = DB::connection('mysql2')
            ->table('compound_fg_links')
            ->where('cpd_id', $request->cpd_id)
            ->where('fg_lot_no', $request->fg_lot_no)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'FG Lot นี้ถูก map กับ CPD แล้ว'
            ]);
        }

        DB::connection('mysql2')
            ->table('compound_fg_links')
            ->insert([
                'cpd_id'     => $request->cpd_id,
                'fg_lot_no'  => $request->fg_lot_no,
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
     * ดึง FG 1 รายการ (edit)
     */
    public function show($id)
    {
        $fg = DB::connection('mysql2')
            ->table('compound_fg_links')
            ->where('id', $id)
            ->first();

        if (!$fg) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบข้อมูล'
            ]);
        }

        return response()->json($fg);
    }

    /**
     * แก้ไข FG
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'fg_lot_no' => 'required|string|max:50',
            'remak'     => 'nullable|string|max:255',
        ]);

        DB::connection('mysql2')
            ->table('compound_fg_links')
            ->where('id', $id)
            ->update([
                'fg_lot_no' => $request->fg_lot_no,
                'remak'     => $request->remak,
            ]);

        return response()->json([
            'success' => true,
            'message' => 'แก้ไขข้อมูลสำเร็จ'
        ]);
    }

    /**
     * ลบ FG
     */
    public function destroy($id)
    {
        DB::connection('mysql2')
            ->table('compound_fg_links')
            ->where('id', $id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'ลบข้อมูลสำเร็จ'
        ]);
    }
}
