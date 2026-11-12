<?php
//UserSeeder.php
namespace Database\Seeders;

use App\Models\OperatingUnit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $pmo = OperatingUnit::where('code', 'PMO')->first();

        // 1. Superadmin / PMO Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@bpi.gov.ph'],
            [
                'name' => 'PMO Administrator',
                'designation' => 'Superadmin / IT Lead',
                'password' => 'password123',
                'operating_unit_id' => $pmo?->id,
                'is_active' => true,
                'approval_status' => 'approved',
            ]
        );
        $admin->syncRoles(['superadmin', 'admin']);

        // 2. Evaluator (Engr. R. M. Ramos)
        $evaluator = User::updateOrCreate(
            ['email' => 'evaluator@bpi.gov.ph'],
            [
                'name' => 'Engr. R. M. Ramos',
                'designation' => 'Planning Officer IV / PMO Evaluator',
                'password' => 'password123',
                'operating_unit_id' => $pmo?->id,
                'is_active' => true,
                'approval_status' => 'approved',
            ]
        );
        $evaluator->syncRoles(['evaluator']);
    }
}
