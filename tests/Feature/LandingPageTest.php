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

        $response->assertOk()
            ->assertViewHas('operatingUnitsCount', 1)
            ->assertSee('1<span', false)
            ->assertSee('CPMD')
            ->assertDontSee('Planning and Management Office')
            ->assertDontSee('DUMMY');

        $this->assertDatabaseHas('operating_units', [
            'code' => 'PMO',
            'is_active' => true,
        ]);
    }

    /**
     * Test categorized operating units directory with national centers excludes PMO.
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
            'name' => 'Planning and Management Office',
            'code' => 'PMO',
            'type' => 'Office',
            'is_active' => true,
        ]);

        OperatingUnit::create([
            'name' => 'Baguio National Crop Research Development Center',
            'code' => 'BNCRDC',
            'type' => 'Center',
            'is_active' => true,
        ]);

        $response = $this->get('/');

        $response->assertOk()
            ->assertViewHas('operatingUnitsCount', 2)
            ->assertSee('Office Technical Divisions')
            ->assertSee('National Crop Research')
            ->assertSee('BNCRDC')
            ->assertSee('CPMD')
            ->assertDontSee('xl:grid-cols-3', false)
            ->assertDontSee('Planning and Management Office');
    }

    public function test_landing_page_still_excludes_pmo_when_no_covered_units_are_active(): void
    {
        OperatingUnit::create([
            'name' => 'Planning and Management Office',
            'code' => 'PMO',
            'type' => 'Office',
            'is_active' => true,
        ]);

        OperatingUnit::create([
            'name' => 'Crop Pest Management Division',
            'code' => 'CPMD',
            'type' => 'Division',
            'is_active' => false,
        ]);

        $response = $this->get('/');

        $response->assertOk()
            ->assertViewHas('operatingUnitsCount', 1)
            ->assertSee('CPMD')
            ->assertDontSee('Planning and Management Office');
    }
}
