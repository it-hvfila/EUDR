<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index()
    {
        return view('Invoice');
    }

    public function search(Request $request)
    {
        $request->validate([
            'pack_id' => 'required'
        ]);

        $packId = $request->pack_id;

        // $url_pilot = 'https://erpepicor.hvfila.com/pilot/api/v1/BaqSvc/HVF_PackInvoice_API2(164958)?';
        $url_production = 'https://erpepicor.hvfila.com/production/api/v1/BaqSvc/HVF_PackInvoice_API2(164958)?';

        $response = Http::withBasicAuth(
            config('services.epicor.username'),
            config('services.epicor.password')
        )->get($url_production, [
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
            'pack_id'       => $first['ShipHead_PackNum'],
            'invoice_no'    => $first['ShipHead_LegalNumber'],
            'shipment_date' => $first['ShipHead_ShipDate'],
            'customer'      => $first['Customer_Name'],
            'province'      => $first['Customer_State'],
            'province_zip'  => $first['Customer_Zip'],
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
                'lf.file_path'
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
                ->pluck('file_path')
                ->filter()
                ->values()
                ->toArray();

            return [
                'lots'        => $row['ShipDtl_LotNum'],
                'geojson'     => $geoFiles,
                'part_num'    => $row['ShipDtl_PartNum'],
                'date_product' => $row['Calculated_date_frist'],
                'description' => $row['ShipDtl_LineDesc'],
                'qty'         => $row['ShipDtl_OurInventoryShipQty'],
                'net_weight'  => $row['Calculated_Cal_NetWeight'],
                'gross_weight' => $row['Calculated_Cal_GrossWeight'],
                'ctns'        => $row['Calculated_Cal_CTNS'],
                'cf_qty'      => $row['Calculated_CF_QTY'],
            ];
        });

        $hasGeoJson = $details->contains(function ($item) {
            return !empty($item['geojson']);
        });

        $searched = true;

        return view('Invoice', [
            'header'      => $header,
            'details'     => $details,
            'packId'      => $packId,
            'hasGeoJson'  => $hasGeoJson,
            'searched'    => $searched
        ]);
    }

    public function download($token)
    {
        $doc = DB::connection('mysql2')
            ->table('company_documents')
            ->where('token', $token)
            ->first();

        if (!$doc) {
            abort(404);
        }

        $path = storage_path('app/private/company_docs/' . $doc->file_name);

        return response()->download($path, $doc->original_name);
    }
}
