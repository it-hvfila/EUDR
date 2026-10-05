<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index()
    {
        return view('Invoice', [
            'searched' => false,
            'supplierData' => collect() // เปลี่ยนจาก suppliersWithDocs
        ]);
    }


    public function search(Request $request)
    {
        $request->validate([
            'pack_id' => 'required|integer|min:1'
        ]);

        $packId = $request->pack_id;

        $url_pilot = 'https://192.168.210.1/pilot/api/v1/BaqSvc/HVF_PackInvoice_API2(164958)?';
        // $url_production = 'https://erpepicor.hvfila.com/production/api/v1/BaqSvc/HVF_PackInvoice_API2(164958)?';

        // $response = Http::withBasicAuth(
        //     config('services.epicor.username'),
        //     config('services.epicor.password')
        // )->get($url_pilot, [
        //             'pack_id' => $packId
        //         ]);

        $response = Http::withoutVerifying() // 👈 ใส่คำสั่งนี้เพื่อสั่ง cURL ข้ามการตรวจสอบใบรับรอง SSL
            ->withBasicAuth(
                config('services.epicor.username'),
                config('services.epicor.password')
            )->get($url_pilot, [
                    'pack_id' => $packId
                ]);

        $data = $response->json();

        if (!isset($data['value']) || count($data['value']) === 0) {
            return back()->withErrors(['ไม่พบข้อมูล Pack ID นี้']);
        }

        $rows = $data['value'];

        /*
        |--------------------------------------------------------------------------
        | Header
        |--------------------------------------------------------------------------
        */
        $first = $rows[0];

        $header = [
            'pack_id' => $first['ShipHead_PackNum'],
            'company_code' => (string) ($first['ShipHead_Company'] ?? $first['ShipDtl_Company'] ?? 'HVF'),
            'epicor_customer_code' => isset($first['Customer_CustID']) ? (string) $first['Customer_CustID'] : (isset($first['Customer_CustNum']) ? (string) $first['Customer_CustNum'] : null),
            'invoice_no' => $first['ShipHead_LegalNumber'],
            'shipment_date' => $first['ShipHead_ShipDate'],
            'customer' => $first['Customer_Name'],
            'province' => $first['Customer_State'],
            'province_zip' => $first['Customer_Zip'],
            'address' => trim(
                ($first['Customer_Address1'] ?? '') . ' ' .
                ($first['Customer_Address2'] ?? '') . ' ' .
                ($first['Customer_Address3'] ?? '') . ' ' .
                ($first['Customer_City'] ?? '') . ' ' .
                ($first['Customer_State'] ?? '') . ' ' .
                ($first['Customer_Zip'] ?? '')
            ),
        ];

        /*
        |--------------------------------------------------------------------------
        | 1️⃣ ดึง FG Lot จาก Epicor
        |--------------------------------------------------------------------------
        */
        $fgLots = collect($rows)
            ->pluck('ShipDtl_LotNum')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ Join หา GeoJSON
        |--------------------------------------------------------------------------
        */
        $geoData = DB::connection('mysql2')
            ->table('compound_fg_links as fg')
            ->join('compound_lots_links as cl', 'fg.cpd_id', '=', 'cl.id')
            ->join('lots as l', 'cl.lot_id', '=', 'l.id')
            ->leftJoin('lot_files as lf', 'l.id', '=', 'lf.lot_id')
            ->whereIn('fg.fg_lot_no', $fgLots)
            ->select(
                'fg.fg_lot_no',
                'lf.file_path',
                'lf.id',
                'lf.file_name'
            )
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 3️⃣ รวมข้อมูลเข้า detail
        |--------------------------------------------------------------------------
        */
        $details = collect($rows)->map(function ($row) use ($geoData) {

            $geoFiles = $geoData
                ->where('fg_lot_no', $row['ShipDtl_LotNum'])
                ->filter(fn ($file) => $file->id && $file->file_path)
                ->unique('id')
                ->map(fn ($file) => ['id' => $file->id, 'file_path' => $file->file_path, 'file_name' => $file->file_name])
                ->values()
                ->toArray();

            return [
                'lots' => $row['ShipDtl_LotNum'],
                'geojson' => $geoFiles,
                'part_num' => $row['ShipDtl_PartNum'],
                'date_product' => $row['Calculated_date_frist'],
                'description' => $row['ShipDtl_LineDesc'],
                'qty' => $row['ShipDtl_OurInventoryShipQty'],
                'net_weight' => $row['Calculated_Cal_NetWeight'],
                'gross_weight' => $row['Calculated_Cal_GrossWeight'],
                'ctns' => $row['Calculated_Cal_CTNS'],
                'cf_qty' => $row['Calculated_CF_QTY'],
            ];
        });

        $hasGeoJson = $details->contains(function ($item) {
            return !empty($item['geojson']);
        });

        /*
        |--------------------------------------------------------------------------
        | 4️⃣ ดึง Suppliers และเอกสารของ Supplier ตาม FG Lots
        |--------------------------------------------------------------------------
        */
        $suppliers = DB::connection('mysql2')
            ->table('compound_fg_links as fg')
            ->join('compound_lots_links as cl', 'fg.cpd_id', '=', 'cl.id')
            ->join('lots as l', 'cl.lot_id', '=', 'l.id')
            ->join('suppliers as s', 'l.supplier_id', '=', 's.id')
            ->whereIn('fg.fg_lot_no', $fgLots)
            ->select('s.id', 's.supplier_code', 's.supplier_name')
            ->distinct()
            ->get();

        $supplierData = $suppliers->map(function ($supplier) {
            $docs = DB::connection('mysql2')
                ->table('supplier_documents as sd')
                ->join('document_categories as dc', 'sd.category_id', '=', 'dc.id')
                ->where('sd.supplier_id', $supplier->id)
                ->select('sd.*', 'dc.category_name', 'dc.report_section')
                ->get()
                ->keyBy('category_id'); // สำคัญ: ช่วยให้ Blade สามารถเรียก $docs[1], $docs[2] ตาม id หมวดหมู่ได้ทันที

            return [
                'supplier' => $supplier,
                'docs' => $docs
            ];
        });

        $snapshot = json_decode(json_encode([
            'header' => $header, 'details' => $details,
            'hasGeoJson' => $hasGeoJson, 'supplierData' => $supplierData,
        ], JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
        $reports = app(\App\Services\CustomerReports::class);
        if ($report = $reports->find($snapshot)) {
            return response()->view('Invoice', $reports->viewData($report))
                ->header('Cache-Control', 'no-store, private');
        }
        $contextToken = bin2hex(random_bytes(16));
        // One pending search per employee session; never trust report fields in POST.
        $request->session()->put('invoice_pending', [
            'token' => $contextToken, 'data' => $snapshot, 'expires' => now()->addMinutes(30)->timestamp,
        ]);
        return view('Invoice', [
            'header' => $header, 'details' => $details, 'packId' => $packId,
            'hasGeoJson' => $hasGeoJson, 'searched' => true, 'supplierData' => $supplierData,
            'contextToken' => $contextToken,
        ]);
    }

    public function createReport(Request $request, \App\Services\CustomerReports $reports)
    {
        $request->validate(['context_token' => 'required|string']);
        $pending = $request->session()->get('invoice_pending');
        abort_unless($pending && hash_equals($pending['token'], $request->context_token)
            && $pending['expires'] > now()->timestamp, 422, 'กรุณาค้นหา Pack ID อีกครั้ง');
        $creator = DB::connection('mysql2')->table('users')
            ->where('username', session('users.username'))->value('id');
        [$report, $password] = $reports->create($pending['data'], $creator);
        return response()->view('Invoice', $reports->viewData($report) + [
            'newPassword' => $password,
            'reportAccount' => DB::connection('mysql2')->table('customers')->where('id', $report->customer_id)->first(),
        ])->header('Cache-Control', 'no-store, private');
    }

    public function resetReportPassword(Request $request, $token)
    {
        $password = \Illuminate\Support\Str::random(20);
        $report = DB::connection('mysql2')->transaction(function () use ($token, $password) {
            $db = DB::connection('mysql2');
            $report = $db->table('customer_reports')->where('token', $token)->lockForUpdate()->first();
            abort_unless($report && $report->is_active && \Carbon\Carbon::parse($report->expired_at)->isFuture(), 403);
            $account = $db->table('customers')->where('id', $report->customer_id)->first();
            abort_unless($account && $account->is_active && \Carbon\Carbon::parse($account->expired_at)->isFuture(), 403);
            $db->table('customers')->where('id', $report->customer_id)->update([
                'password' => \Illuminate\Support\Facades\Hash::make($password), 'last_login' => null,
            ]);
            return $report;
        });
        return response()->view('Invoice', app(\App\Services\CustomerReports::class)->viewData($report) + [
            'newPassword' => $password,
            'reportAccount' => DB::connection('mysql2')->table('customers')->where('id', $report->customer_id)->first(),
        ])->header('Cache-Control', 'no-store, private');
    }



    public function downloadGeojson($filename)
    {
        // แปลง path ให้เป็น path ภายใน public เท่านั้น
        $filename = ltrim($filename, '/');

        $path = \App\Services\PrivateReportFiles::existing($filename);

        // Debug ชั่วคราว
        if (!file_exists($path)) {
            abort(404, 'ไม่พบไฟล์: ' . $path);
        }

        return response()->download($path, basename($path), [
            'Content-Type' => 'application/octet-stream',
        ]);
    }
}