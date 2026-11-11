<?php

namespace Tests\Feature;

use App\Models\OperatingUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the landing page displays the dynamic operating units count.
     */
    public function test_landing_page_displays_dynamic_operating_units_count(): void
    {
        OperatingUnit::create([
            'name' => 'Crop Pest Management Division',
            'code' => 'CPMD',
            'type' => 'Division',
            'is_active' => true,
        ]);

        OperatingUnit::create([
            'name' => 'Planning and Management Office',
            'code' => 'PMO',
            'type' => 'Office',
            'is_active' => true,
        ]);

        OperatingUnit::create([
            'name' => 'Decommissioned Unit',
            'code' => 'DUMMY',
            'type' => 'Division',
            'is_active' => false,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('operatingUnitsCount', 2);
        $response->assertSee('2<span', false);
        $response->assertSee('CPMD');
        $response->assertSee('PMO');
        $response->assertDontSee('DUMMY');
    }

    /**
     * Test categorized operating units directory with national centers.
     */
    public function test_landing_page_renders_categorized_units_when_centers_present(): void
    {
        OperatingUnit::create([
            'name' => 'Crop Pest Management Division',
            'code' => 'CPMD',
            'type' => 'Division',
            'is_active' => true,
        ]);

        OperatingUnit::create([
            'name' => 'Baguio National Crop Research Development Center',
            'code' => 'BNCRDC',
            'type' => 'Center',
            'is_active' => true,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Office Technical Divisions');
        $response->assertSee('National Crop Research');
        $response->assertSee('BNCRDC');
        $response->assertSee('CPMD');
    }
}
