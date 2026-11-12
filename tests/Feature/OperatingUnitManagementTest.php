<?php

namespace Tests\Feature;

use App\Models\OperatingUnit;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OperatingUnitManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    /**
     * Create an authenticated admin user for operating unit tests.
     */
    private function createAdminUser(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        return $admin;
    }

    public function test_operating_units_can_be_sorted_ascending_and_descending_by_name_and_code(): void
    {
        $admin = $this->createAdminUser();

        OperatingUnit::create(['name' => 'Zebra Division', 'code' => 'ZBR', 'type' => 'Division', 'is_active' => true]);
        OperatingUnit::create(['name' => 'Alpha Division', 'code' => 'ALP', 'type' => 'Division', 'is_active' => true]);
        OperatingUnit::create(['name' => 'Mango Division', 'code' => 'MNG', 'type' => 'Division', 'is_active' => true]);

        $ascResponse = $this->actingAs($admin)->get(route('admin.operating-units.index', [
            'sort' => 'name',
            'direction' => 'asc',
        ]));

        $ascResponse->assertOk()
            ->assertSeeInOrder(['Alpha Division', 'Mango Division', 'Zebra Division'])
            ->assertSee('direction=desc', false);

        $descResponse = $this->actingAs($admin)->get(route('admin.operating-units.index', [
            'sort' => 'name',
            'direction' => 'desc',
        ]));

        $descResponse->assertOk()
            ->assertSeeInOrder(['Zebra Division', 'Mango Division', 'Alpha Division'])
            ->assertSee('direction=asc', false);
    }

    public function test_sorting_preserves_active_filters_and_ignores_invalid_sort_columns(): void
    {
        $admin = $this->createAdminUser();

        OperatingUnit::create(['name' => 'Beta Division', 'code' => 'BET', 'type' => 'Division', 'is_active' => true]);
        OperatingUnit::create(['name' => 'Alpha Division', 'code' => 'ALP', 'type' => 'Division', 'is_active' => true]);
        OperatingUnit::create(['name' => 'Zeta Regional Station', 'code' => 'ZRS', 'type' => 'Office', 'is_active' => false]);

        $filteredSorted = $this->actingAs($admin)->get(route('admin.operating-units.index', [
            'type' => 'Division',
            'status' => '1',
            'sort' => 'name',
            'direction' => 'asc',
        ]));

        $filteredSorted->assertOk()
            ->assertSeeInOrder(['Alpha Division', 'Beta Division'])
            ->assertDontSee('Zeta Regional Station')
            ->assertSee('type=Division', false)
            ->assertSee('status=1', false)
            ->assertSee('<input type="hidden" name="sort" value="name">', false)
            ->assertSee('<input type="hidden" name="direction" value="asc">', false);

        $invalidSort = $this->actingAs($admin)->get(route('admin.operating-units.index', [
            'sort' => 'malicious_column',
            'direction' => 'invalid',
        ]));

        $invalidSort->assertOk()
            ->assertSee('Alpha Division')
            ->assertSee('Beta Division');
    }

    public function test_operating_unit_code_is_normalized_to_uppercase_on_create_update_and_read(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin)->post(route('admin.operating-units.store'), [
            'name' => 'Crop Pest Management Division',
            'code' => ' cpmd ',
            'type' => 'Division',
            'is_active' => '1',
        ])->assertRedirect(route('admin.operating-units.index'));

        $this->assertDatabaseHas('operating_units', [
            'name' => 'Crop Pest Management Division',
            'code' => 'CPMD',
        ]);

        // Duplicate lowercase acronym must fail unique validation after normalization.
        $this->actingAs($admin)->post(route('admin.operating-units.store'), [
            'name' => 'Duplicate Division',
            'code' => 'cpmd',
            'type' => 'Division',
        ])->assertSessionHasErrors('code');

        $unit = OperatingUnit::where('code', 'CPMD')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.operating-units.update', $unit), [
            'name' => 'Crop Pest Management Division',
            'code' => ' cpmd-new ',
            'type' => 'Division',
            'is_active' => '1',
        ])->assertRedirect(route('admin.operating-units.index'));

        $this->assertDatabaseHas('operating_units', [
            'id' => $unit->id,
            'code' => 'CPMD-NEW',
        ]);

        // Pre-existing lowercase rows in storage still render uppercase when accessed.
        DB::table('operating_units')->where('id', $unit->id)->update(['code' => 'legacy']);
        $this->assertSame('LEGACY', $unit->fresh()->code);
    }

    public function test_operating_unit_forms_and_index_render_uniform_classification_dropdown_options(): void
    {
        $admin = $this->createAdminUser();
        $unit = OperatingUnit::create([
            'name' => 'Legacy Experimental Unit',
            'code' => 'LEU',
            'type' => 'Legacy Custom',
            'is_active' => true,
        ]);

        $expectedOptions = [
            'Division',
            'Center',
            'Office',
        ];
        $removedOptions = [
            'National Center',
            'Station',
            'Section',
            'Unit',
            'Laboratory',
        ];

        $createResponse = $this->actingAs($admin)->get(route('admin.operating-units.create'));
        $createResponse->assertOk()
            ->assertSee('<select name="type"', false)
            ->assertDontSee('types_list');

        $editResponse = $this->actingAs($admin)->get(route('admin.operating-units.edit', $unit));
        $editResponse->assertOk()
            ->assertSee('<select name="type"', false)
            ->assertSee('Legacy Custom (Non-standard)')
            ->assertDontSee('types_list');

        $indexResponse = $this->actingAs($admin)->get(route('admin.operating-units.index'));
        $indexResponse->assertOk();

        foreach ($expectedOptions as $option) {
            $createResponse->assertSee('<option value="'.$option.'"', false);
            $editResponse->assertSee('<option value="'.$option.'"', false);
            $indexResponse->assertSee('<option value="'.$option.'"', false);
        }

        foreach ($removedOptions as $removedOption) {
            $createResponse->assertDontSee('<option value="'.$removedOption.'"', false);
            $editResponse->assertDontSee('<option value="'.$removedOption.'"', false);
            $indexResponse->assertDontSee('<option value="'.$removedOption.'"', false);
        }
    }

    public function test_operating_unit_classification_type_accepts_uniform_options_and_rejects_arbitrary_values(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin)->post(route('admin.operating-units.store'), [
            'name' => 'Baguio Crops Research Center',
            'code' => 'BCRC',
            'type' => 'Center',
            'is_active' => '1',
        ])->assertRedirect(route('admin.operating-units.index'));

        $this->assertDatabaseHas('operating_units', [
            'code' => 'BCRC',
            'type' => 'Center',
        ]);

        $this->actingAs($admin)->post(route('admin.operating-units.store'), [
            'name' => 'Invalid Unit',
            'code' => 'INV',
            'type' => 'Station',
            'is_active' => '1',
        ])->assertSessionHasErrors('type');

        $this->assertDatabaseMissing('operating_units', [
            'code' => 'INV',
        ]);

        $unit = OperatingUnit::where('code', 'BCRC')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.operating-units.update', $unit), [
            'name' => 'Baguio Crops Research Center',
            'code' => 'BCRC',
            'type' => 'Office',
            'is_active' => '1',
        ])->assertRedirect(route('admin.operating-units.index'));

        $this->assertDatabaseHas('operating_units', [
            'id' => $unit->id,
            'type' => 'Office',
        ]);

        $this->actingAs($admin)->put(route('admin.operating-units.update', $unit), [
            'name' => 'Baguio Crops Research Center',
            'code' => 'BCRC',
            'type' => 'National Center',
            'is_active' => '1',
        ])->assertSessionHasErrors('type');

        $this->assertSame('Office', $unit->fresh()->type);

        $this->actingAs($admin)->put(route('admin.operating-units.update', $unit), [
            'name' => 'Baguio Crops Research Center',
            'code' => 'BCRC',
            'type' => '',
            'is_active' => '1',
        ])->assertRedirect(route('admin.operating-units.index'));

        $this->assertNull($unit->fresh()->type);
    }
}
