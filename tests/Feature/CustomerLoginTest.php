<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerLoginTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.connections.mysql2' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']]);
        DB::purge('mysql2');
        $db = DB::connection('mysql2');
        $db->statement('CREATE TABLE customers (id INTEGER PRIMARY KEY, username TEXT, password TEXT, customer_name TEXT, is_active INTEGER, expired_at TEXT, last_login TEXT)');
        $db->statement('CREATE TABLE customer_reports (id INTEGER PRIMARY KEY, customer_id INTEGER, pack_id TEXT, invoice_no TEXT, is_active INTEGER, expired_at TEXT, created_at TEXT, token TEXT)');
        $db->table('customers')->insert([
            'id' => 1, 'username' => 'customer', 'password' => Hash::make('secret'),
            'customer_name' => 'Customer One', 'is_active' => 1, 'expired_at' => now()->addDay(),
        ]);
    }

    public function test_login_validates_and_rejects_bad_password(): void
    {
        $this->postJson('/portal/login', [])->assertUnprocessable();
        $this->postJson('/portal/login', ['username' => 'customer', 'password' => 'wrong'])
            ->assertUnauthorized()->assertSessionMissing('customer');
    }

    public function test_login_sets_customer_session_and_returns_intended_url(): void
    {
        $this->withSession(['customer_intended' => url('/portal/download-list')])
            ->postJson('/portal/login', ['username' => 'customer', 'password' => 'secret'])
            ->assertOk()->assertJsonPath('redirect_url', url('/portal/download-list'))
            ->assertSessionHas('customer.customer_id', 1)->assertSessionMissing('customer_intended');
        $this->assertNotNull(DB::connection('mysql2')->table('customers')->value('last_login'));
    }

    public function test_expired_and_disabled_accounts_cannot_login(): void
    {
        foreach ([['expired_at' => now()->subMinute()], ['expired_at' => now()->addDay(), 'is_active' => 0]] as $changes) {
            DB::connection('mysql2')->table('customers')->update($changes);
            $this->postJson('/portal/login', ['username' => 'customer', 'password' => 'secret'])->assertUnauthorized();
        }
    }

    public function test_middleware_remembers_destination_and_revokes_disabled_session(): void
    {
        $this->get('/portal/download-list')->assertRedirect(route('customer.login'))
            ->assertSessionHas('customer_intended', url('/portal/download-list'));
    }

    public function test_middleware_revokes_disabled_session(): void
    {
        DB::connection('mysql2')->table('customers')->update(['is_active' => 0]);
        $this->withSession(['customer' => ['customer_id' => 1, 'credential_version' => hash('sha256', DB::connection('mysql2')->table('customers')->value('password'))]])->get('/portal/download-list')
            ->assertRedirect(route('customer.login'));
        $this->assertFalse(session()->has('customer'));
    }

    public function test_dashboard_only_shows_current_customers_active_reports(): void
    {
        foreach ([[1, 'MY-PACK', 1, now()->addDay()], [2, 'OTHER-PACK', 1, now()->addDay()],
            [1, 'REVOKED-PACK', 0, now()->addDay()], [1, 'EXPIRED-PACK', 1, now()->subDay()]] as $row) {
            DB::connection('mysql2')->table('customer_reports')->insert([
                'customer_id' => $row[0], 'pack_id' => $row[1], 'is_active' => $row[2],
                'expired_at' => $row[3], 'created_at' => now(), 'token' => 'report-token',
            ]);
        }
        $this->withSession(['customer' => ['customer_id' => 1, 'credential_version' => hash('sha256', DB::connection('mysql2')->table('customers')->value('password'))]])->get('/portal/download-list')
            ->assertOk()->assertSee('MY-PACK')->assertDontSee('OTHER-PACK')
            ->assertDontSee('REVOKED-PACK')->assertDontSee('EXPIRED-PACK');
    }

    public function test_customer_logout_preserves_employee_session(): void
    {
        $this->withSession(['customer' => ['customer_id' => 1, 'credential_version' => hash('sha256', DB::connection('mysql2')->table('customers')->value('password'))], 'users' => ['name' => 'Employee']])
            ->post('/portal/logout')->assertRedirect(route('customer.login'))
            ->assertSessionMissing('customer')->assertSessionHas('users.name', 'Employee');
        $this->get('/portal/logout')->assertStatus(405);
    }

    public function test_repeated_failed_login_is_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $this->postJson('/portal/login', ['username' => 'customer', 'password' => 'wrong'])->assertUnauthorized();
        }
        $this->postJson('/portal/login', ['username' => 'customer', 'password' => 'secret'])->assertStatus(429);
    }
}
