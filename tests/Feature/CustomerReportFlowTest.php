<?php

namespace Tests\Feature;

use App\Services\CustomerReports;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerReportFlowTest extends TestCase
{
    private array $snapshot;
    private string $testPath;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.mysql2' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => ''], 'customer_reports.company_document_tokens' => ['company-token']]);
        DB::purge('mysql2');
        $db = DB::connection('mysql2');
        foreach ([
            'users' => 'id INTEGER PRIMARY KEY, username TEXT',
            'customers' => 'id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT UNIQUE, password TEXT, customer_name TEXT, is_active INTEGER, expired_at TEXT, last_login TEXT, created_by INTEGER',
            'customer_reports' => 'id INTEGER PRIMARY KEY AUTOINCREMENT, customer_id INTEGER UNIQUE, company_code TEXT, pack_id TEXT, invoice_no TEXT, token TEXT, epicor_customer_code TEXT, customer_name TEXT, report_data TEXT, expired_at TEXT, is_active INTEGER DEFAULT 1, created_by INTEGER, created_at TEXT DEFAULT CURRENT_TIMESTAMP, UNIQUE(company_code, pack_id)',
            'customer_report_files' => 'id INTEGER PRIMARY KEY AUTOINCREMENT, report_id INTEGER, token TEXT, lot_file_id INTEGER, supplier_document_id INTEGER, company_document_id INTEGER',
            'company_documents' => 'id INTEGER PRIMARY KEY, token TEXT, file_path TEXT, doc_name TEXT',
            'supplier_documents' => 'id INTEGER PRIMARY KEY, token TEXT, file_path TEXT, doc_name TEXT',
            'lot_files' => 'id INTEGER PRIMARY KEY, lot_id INTEGER, file_path TEXT, file_name TEXT',
            'compound_fg_links' => 'id INTEGER PRIMARY KEY, cpd_id INTEGER, fg_lot_no TEXT',
            'compound_lots_links' => 'id INTEGER PRIMARY KEY, lot_id INTEGER',
            'lots' => 'id INTEGER PRIMARY KEY, supplier_id INTEGER',
            'suppliers' => 'id INTEGER PRIMARY KEY, supplier_name TEXT, supplier_code TEXT',
            'document_categories' => 'id INTEGER PRIMARY KEY, category_name TEXT, report_section TEXT',
            'log_downloads' => 'id INTEGER PRIMARY KEY, customer_id INTEGER, report_id INTEGER, report_file_id INTEGER, topic_name TEXT, file_token TEXT, ip_address TEXT, user_agent TEXT',
        ] as $table => $columns) {
            $db->statement("CREATE TABLE $table ($columns)");
        }
        $this->testPath = 'uploads/lots/test-'.bin2hex(random_bytes(6)).'.geojson';
        $path = \App\Services\PrivateReportFiles::path($this->testPath);
        if (!is_dir(dirname($path))) mkdir(dirname($path), 0750, true);
        file_put_contents($path, '{"type":"FeatureCollection","features":[]}');
        $db->table('lot_files')->insert(['id' => 1, 'file_path' => $this->testPath, 'file_name' => 'origin.geojson']);
        $db->table('company_documents')->insert(['id' => 1, 'token' => 'company-token', 'file_path' => 'uploads/company_doc/example.pdf', 'doc_name' => 'Company']);
        $db->table('supplier_documents')->insert(['id' => 1, 'token' => 'supplier-token', 'file_path' => 'uploads/supplier_doc/example.pdf', 'doc_name' => 'Supplier']);
        $db->table('users')->insert(['id' => 1, 'username' => 'sale']);
        $this->snapshot = [
            'header' => ['pack_id' => '123', 'company_code' => 'HVF', 'epicor_customer_code' => 'C1', 'customer' => 'Client', 'invoice_no' => 'INV1', 'shipment_date' => '2026-10-01', 'address' => 'Address'],
            'details' => [['lots' => 'FG1', 'geojson' => [['id' => 1, 'file_path' => $this->testPath]], 'description' => 'Rubber', 'qty' => 1, 'net_weight' => 1, 'gross_weight' => 2, 'cf_qty' => 1, 'date_product' => '2026-10-01']],
            'hasGeoJson' => true, 'supplierData' => [['supplier' => ['supplier_name' => 'Supplier', 'supplier_code' => 'S1'], 'docs' => [1 => ['id' => 1, 'file_path' => 'uploads/supplier_doc/example.pdf', 'doc_name' => 'Supplier']]]],
        ];
    }

    protected function tearDown(): void
    {
        if (isset($this->testPath)) @unlink(\App\Services\PrivateReportFiles::path($this->testPath));
        parent::tearDown();
    }

    private function create(): array
    {
        return app(CustomerReports::class)->create($this->snapshot, 1);
    }

    private function customerSession($report): array
    {
        $customer = DB::connection('mysql2')->table('customers')->where('id', $report->customer_id)->first();
        return ['customer' => ['customer_id' => $customer->id, 'credential_version' => hash('sha256', $customer->password)]];
    }

    public function test_search_does_not_create_account_and_reuses_existing_report(): void
    {
        \Illuminate\Support\Facades\Http::fake(['*' => \Illuminate\Support\Facades\Http::response(['value' => [[
            'ShipHead_PackNum' => 123, 'ShipHead_Company' => 'HVF', 'Customer_CustID' => 'C1',
            'ShipHead_LegalNumber' => 'INV1', 'ShipHead_ShipDate' => '2026-10-01',
            'Customer_Name' => 'Client', 'Customer_State' => '', 'Customer_Zip' => '',
            'ShipDtl_LotNum' => 'FG1', 'ShipDtl_PartNum' => 'Rubber', 'Calculated_date_frist' => '2026-10-01',
            'ShipDtl_LineDesc' => 'Rubber', 'ShipDtl_OurInventoryShipQty' => 1,
            'Calculated_Cal_NetWeight' => 1, 'Calculated_Cal_GrossWeight' => 2,
            'Calculated_Cal_CTNS' => 1, 'Calculated_CF_QTY' => 1,
        ]]])]);
        $this->withSession(['users' => ['username' => 'sale']])->post('/invoice/search', ['pack_id' => 123])
            ->assertOk()->assertSee('สร้างรายงานสำหรับลูกค้า');
        $this->assertSame(0, DB::connection('mysql2')->table('customers')->count());
        $this->assertSame('C1', session('invoice_pending.data.header.epicor_customer_code'));
        $this->create();
        $this->post('/invoice/search', ['pack_id' => 123])->assertOk()->assertSee('รายงานนี้สร้างบัญชีลูกค้าแล้ว');
        $this->assertSame(1, DB::connection('mysql2')->table('customers')->count());
    }

    public function test_linked_evidence_cannot_be_deleted_or_overwritten(): void
    {
        $this->create();
        $this->withSession(['users' => ['username' => 'sale']])->delete('/lots/file/1')->assertStatus(409);
        $this->delete('/supplier_docs/delete/1')->assertStatus(409);
        $this->delete('/company_docs/delete/1')->assertStatus(409);
        $this->post('/company_docs/save', ['doc_id' => 1, 'doc_name' => 'Changed', 'category_id' => 1])->assertStatus(409);
        $this->assertFileExists(\App\Services\PrivateReportFiles::path($this->testPath));
    }

    public function test_creation_and_retry_use_one_account_and_three_sources(): void
    {
        [$report, $password] = $this->create();
        $this->assertTrue(Hash::check($password, DB::connection('mysql2')->table('customers')->value('password')));
        [$again, $secondPassword] = $this->create();
        $this->assertSame($report->id, $again->id);
        $this->assertNull($secondPassword);
        $this->assertSame(1, DB::connection('mysql2')->table('customers')->count());
        $this->assertSame(3, DB::connection('mysql2')->table('customer_report_files')->count());
    }

    public function test_company_scopes_pack_uniqueness(): void
    {
        $this->create();
        $this->snapshot['header']['company_code'] = 'OTHER';
        $this->create();
        $this->assertSame(2, DB::connection('mysql2')->table('customer_reports')->count());
    }

    public function test_transaction_rolls_back_account_on_invalid_snapshot(): void
    {
        unset($this->snapshot['header']['invoice_no']);
        try { $this->create(); $this->fail('Expected exception'); } catch (\Throwable $exception) {
            $this->assertSame(0, DB::connection('mysql2')->table('customers')->count());
            $this->assertSame(0, DB::connection('mysql2')->table('customer_reports')->count());
        }
    }

    public function test_employee_creates_from_server_context_and_pdf_has_protected_links(): void
    {
        $this->withSession(['users' => ['username' => 'sale'], 'invoice_pending' => [
            'token' => 'context', 'expires' => now()->addMinute()->timestamp, 'data' => $this->snapshot,
        ]])->post('/invoice/report', ['context_token' => 'context', 'customer_id' => 999])
            ->assertOk()->assertSee('แสดงรหัสผ่านครั้งนี้เท่านั้น')->assertSee('/portal/reports/')
            ->assertDontSee('company_docs/download/')->assertDontSee('supplier_docs/download/')
            ->assertDontSee(url($this->testPath));
        $this->assertSame(1, DB::connection('mysql2')->table('customers')->count());
    }

    public function test_creation_requires_employee_session_and_valid_context(): void
    {
        $this->post('/invoice/report', ['context_token' => 'fake'])->assertRedirect('/');
        $this->withSession(['users' => ['username' => 'sale']])->post('/invoice/report', ['context_token' => 'fake'])->assertStatus(422);
        $this->assertSame(0, DB::connection('mysql2')->table('customers')->count());
    }

    public function test_download_requires_login_then_serves_file_and_logs(): void
    {
        [$report] = $this->create();
        $file = DB::connection('mysql2')->table('customer_report_files')->whereNotNull('lot_file_id')->first();
        $url = route('customer.report.download', [$report->token, $file->token]);
        $this->get($url)->assertRedirect(route('customer.login'))->assertSessionHas('customer_intended', $url);
        $this->withSession($this->customerSession($report))->get($url)->assertOk()->assertDownload('origin.geojson');
        $this->assertSame(1, DB::connection('mysql2')->table('log_downloads')->count());
    }

    public function test_customer_sees_report_topics_without_employee_controls(): void
    {
        [$report] = $this->create();
        $this->withSession($this->customerSession($report))
            ->get(route('customer.report.files', $report->token))->assertOk()
            ->assertSee('EUDR Data Sheet')->assertSee('Document Evidence')
            ->assertSee('Shipment')->assertSee('Legal Compliance')
            ->assertSee('/portal/reports/')->assertSee(asset('assets/img/logo-hvfilla.png'))
            ->assertDontSee('id="searchForm"', false)
            ->assertDontSee('รีเซ็ตรหัสผ่านลูกค้า')->assertDontSee('รายงานนี้สร้างบัญชีลูกค้าแล้ว')
            ->assertDontSee('id="sidebar"', false);
    }

    public function test_other_customer_cannot_read_or_download_report(): void
    {
        [$report] = $this->create();
        $this->snapshot['header']['pack_id'] = '456';
        [$other] = $this->create();
        $file = DB::connection('mysql2')->table('customer_report_files')->where('report_id', $report->id)->first();
        $this->withSession($this->customerSession($other))->get(route('customer.report.files', $report->token))->assertNotFound();
        $this->get(route('customer.report.download', [$report->token, $file->token]))->assertNotFound();
        $this->assertSame(0, DB::connection('mysql2')->table('log_downloads')->count());
    }

    public function test_expired_or_revoked_report_denied(): void
    {
        [$report] = $this->create();
        foreach ([['expired_at' => now()->subMinute()], ['expired_at' => now()->addDay(), 'is_active' => 0]] as $changes) {
            DB::connection('mysql2')->table('customer_reports')->where('id', $report->id)->update($changes);
            $this->withSession($this->customerSession($report))->get(route('customer.report.files', $report->token))->assertNotFound();
        }
    }

    public function test_file_token_from_other_report_denied(): void
    {
        [$report] = $this->create();
        $this->snapshot['header']['pack_id'] = '456';
        [$other] = $this->create();
        $file = DB::connection('mysql2')->table('customer_report_files')->where('report_id', $other->id)->first();
        $this->withSession($this->customerSession($report))->get(route('customer.report.download', [$report->token, $file->token]))->assertNotFound();
    }

    public function test_password_reset_invalidates_existing_customer_session(): void
    {
        [$report, $password] = $this->create();
        $session = $this->customerSession($report);
        $this->withSession($session + ['users' => ['username' => 'sale']])
            ->post(route('invoice.report.reset', $report->token))->assertOk();
        $this->assertFalse(Hash::check($password, DB::connection('mysql2')->table('customers')->value('password')));
        $this->get(route('customer.dashboard'))->assertRedirect(route('customer.login'));
    }

    public function test_legacy_routes_require_employee_login(): void
    {
        $this->get('/company_docs/download/company-token')->assertRedirect('/');
        $this->get('/download-geojson/uploads/lots/file.geojson')->assertRedirect('/');
        $this->get('/supplier_docs/download/supplier-token')->assertRedirect('/');
    }

    public function test_path_traversal_is_rejected(): void
    {
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        \App\Services\PrivateReportFiles::existing('uploads/lots/../../.env');
    }
}
