<?php
//DatabaseSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            PermissionSeeder::class,
            OperatingUnitSeeder::class,
            PAPSeeder::class,
            BpiProgramCatalogSeeder::class,
            UserSeeder::class,
        ]);
    }
}
