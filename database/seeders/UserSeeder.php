<?php

namespace Database\Seeders;

use App\Models\OperatingUnit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds with author names.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $pmo = OperatingUnit::where('code', 'PMO')->first();
        $cpmd = OperatingUnit::where('code', 'CPMD')->first() ?? OperatingUnit::first();

        $defaultPassword = Hash::make('Password123!');

        $accounts = [
            [
                'email' => 'superadmin@bpi.gov.ph',
                'name' => 'William Shakespeare',
                'designation' => 'Lead Systems Architect & Superadmin',
                'operating_unit_id' => $pmo?->id,
                'roles' => ['superadmin'],
            ],
            [
                'email' => 'admin@bpi.gov.ph',
                'name' => 'Arthur Conan Doyle',
                'designation' => 'PMO Administrator',
                'operating_unit_id' => $pmo?->id,
                'roles' => ['admin'],
            ],
            [
                'email' => 'evaluator@bpi.gov.ph',
                'name' => 'Franz Kafka',
                'designation' => 'PMO Lead Evaluator',
                'operating_unit_id' => $pmo?->id,
                'roles' => ['evaluator'],
            ],
            [
                'email' => 'chief@bpi.gov.ph',
                'name' => 'George Eliot',
                'designation' => 'Chief, Crop Pest Management Division',
                'operating_unit_id' => $cpmd?->id,
                'roles' => ['ou-head'],
            ],
            [
                'email' => 'encoder@bpi.gov.ph',
                'name' => 'Jose Dalisay',
                'designation' => 'Planning Officer / Designated Encoder',
                'operating_unit_id' => $cpmd?->id,
                'roles' => ['encoder'],
            ],
            [
                'email' => 'director@bpi.gov.ph',
                'name' => 'Charles Dickens',
                'designation' => 'Director, Bureau of Plant Industry',
                'operating_unit_id' => $pmo?->id,
                'roles' => ['higher-up'],
            ],
        ];

        foreach ($accounts as $data) {
            $roles = $data['roles'];
            unset($data['roles']);

            $user = User::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, [
                    'password' => $defaultPassword,
                    'is_active' => true,
                    'approval_status' => 'approved',
                    'email_verified_at' => now(),
                ])
            );

            $user->syncRoles($roles);

            // Link as Division Chief / Reporting Officer for Operating Unit
            if (in_array('ou-head', $roles, true) && $user->operating_unit_id) {
                OperatingUnit::where('id', $user->operating_unit_id)->update(['division_chief_id' => $user->id]);
            }
            if (in_array('encoder', $roles, true) && $user->operating_unit_id) {
                OperatingUnit::where('id', $user->operating_unit_id)->update(['reporting_officer_id' => $user->id]);
            }
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
