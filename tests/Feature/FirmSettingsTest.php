<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\FirmSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FirmSettingsTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant1;
    private Tenant $tenant2;
    private User $user1;
    private User $user2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant1 = Tenant::create(['name' => 'Firm Settings Tenant 1', 'domain' => 'firm1.com', 'subscription_status' => 'active']);
        $this->tenant2 = Tenant::create(['name' => 'Firm Settings Tenant 2', 'domain' => 'firm2.com', 'subscription_status' => 'active']);

        $this->user1 = User::create([
            'tenant_id' => $this->tenant1->id,
            'name' => 'CA User 1',
            'email' => 'user1@firm1.com',
            'password' => bcrypt('password'),
        ]);

        $this->user2 = User::create([
            'tenant_id' => $this->tenant2->id,
            'name' => 'CA User 2',
            'email' => 'user2@firm2.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_firm_settings_update_and_tenant_isolation()
    {
        $this->actingAs($this->user1);

        Livewire::test(\App\Livewire\Settings\Firm::class)
            ->set('firm_name', 'Standard Touch CA Firm')
            ->set('ca_reg_number', 'REG998877')
            ->set('gstin', '27AAAAA0000A1Z5')
            ->call('saveSettings')
            ->assertHasNoErrors();

        $setting1 = FirmSetting::where('tenant_id', $this->tenant1->id)->first();
        $this->assertNotNull($setting1);
        $this->assertEquals('Standard Touch CA Firm', $setting1->firm_name);
        $this->assertEquals('REG998877', $setting1->ca_reg_number);

        // Verify Tenant 2 cannot see Tenant 1 firm settings
        $setting2 = FirmSetting::where('tenant_id', $this->tenant2->id)->first();
        $this->assertNull($setting2);
    }
}
