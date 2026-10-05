<?php

namespace App\Services;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CustomerReports
{
    public function find(array $snapshot)
    {
        return DB::connection('mysql2')->table('customer_reports')
            ->where('company_code', $snapshot['header']['company_code'])
            ->where('pack_id', (string) $snapshot['header']['pack_id'])->first();
    }

    public function create(array $snapshot, ?int $creator): array
    {
        if ($report = $this->find($snapshot)) {
            return [$report, null];
        }
        try {
            return DB::connection('mysql2')->transaction(function () use ($snapshot, $creator) {
                $db = DB::connection('mysql2');
                $password = Str::random(20);
                $expires = now()->addDays(config('customer_reports.access_days', 30));
                $header = $snapshot['header'];
                $customerId = $db->table('customers')->insertGetId([
                    'customer_name' => mb_substr($header['customer'], 0, 100),
                    'username' => 'report-'.Str::lower(Str::random(16)),
                    'password' => Hash::make($password), 'expired_at' => $expires,
                    'is_active' => 1, 'created_by' => $creator,
                ]);
                $reportId = $db->table('customer_reports')->insertGetId([
                    'customer_id' => $customerId, 'pack_id' => (string) $header['pack_id'],
                    'company_code' => $header['company_code'],
                    'epicor_customer_code' => $header['epicor_customer_code'],
                    'customer_name' => mb_substr($header['customer'], 0, 100),
                    'invoice_no' => $header['invoice_no'], 'token' => bin2hex(random_bytes(32)),
                    'expired_at' => $expires, 'created_by' => $creator,
                    'report_data' => json_encode($snapshot, JSON_THROW_ON_ERROR),
                ]);
                $sources = [];
                foreach ($snapshot['details'] as $detail) {
                    foreach ($detail['geojson'] as $file) {
                        $sources['lot_file_id:'.$file['id']] = ['lot_file_id', $file['id']];
                    }
                }
                foreach ($snapshot['supplierData'] as $supplier) {
                    foreach ($supplier['docs'] as $doc) {
                        if (!empty($doc['file_path'])) {
                            $sources['supplier_document_id:'.$doc['id']] = ['supplier_document_id', $doc['id']];
                        }
                    }
                }
                foreach ($db->table('company_documents')->whereIn('token', config('customer_reports.company_document_tokens'))->get() as $doc) {
                    if ($doc->file_path) {
                        $sources['company_document_id:'.$doc->id] = ['company_document_id', $doc->id];
                    }
                }
                foreach ($sources as [$column, $id]) {
                    $db->table('customer_report_files')->insert([
                        'report_id' => $reportId, $column => $id, 'token' => bin2hex(random_bytes(32)),
                    ]);
                }
                return [$db->table('customer_reports')->where('id', $reportId)->first(), $password];
            });
        } catch (QueryException $exception) {
            // A simultaneous creation rolled its account back; use the winning report.
            if ($report = $this->find($snapshot)) {
                return [$report, null];
            }
            throw $exception;
        }
    }

    public function viewData($report): array
    {
        $snapshot = json_decode($report->report_data, true, 512, JSON_THROW_ON_ERROR);
        $files = DB::connection('mysql2')->table('customer_report_files')->where('report_id', $report->id)->get();
        $urls = [];
        $missingEvidence = [];
        foreach ($files as $file) {
            foreach (['lot_file_id', 'supplier_document_id', 'company_document_id'] as $column) {
                if ($file->$column) {
                    $table = ['lot_file_id' => 'lot_files', 'supplier_document_id' => 'supplier_documents', 'company_document_id' => 'company_documents'][$column];
                    $source = DB::connection('mysql2')->table($table)->where('id', $file->$column)->first();
                    if (!$source || !$source->file_path || !is_file(PrivateReportFiles::path($source->file_path))) {
                        $missingEvidence[] = $source->file_name ?? $source->doc_name ?? ($table.' #'.$file->$column);
                    }
                    $urls[$column.':'.$file->$column] = route('customer.report.download', [$report->token, $file->token]);
                }
            }
        }
        $companyUrls = [];
        foreach (DB::connection('mysql2')->table('company_documents')->whereIn('id', $files->pluck('company_document_id')->filter())->get() as $doc) {
            $companyUrls[$doc->token] = $urls['company_document_id:'.$doc->id];
        }
        return [
            'header' => $snapshot['header'], 'details' => collect($snapshot['details']),
            'packId' => $snapshot['header']['pack_id'], 'hasGeoJson' => $snapshot['hasGeoJson'],
            'supplierData' => collect($snapshot['supplierData'])->map(fn ($item) => [
                'supplier' => (object) $item['supplier'],
                'docs' => collect($item['docs'])->map(fn ($doc) => (object) $doc),
            ]),
            'reportAccount' => DB::connection('mysql2')->table('customers')->where('id', $report->customer_id)->first(),
            'searched' => true, 'report' => $report, 'evidenceUrls' => $urls, 'companyUrls' => $companyUrls, 'missingEvidence' => $missingEvidence,
        ];
    }
}
