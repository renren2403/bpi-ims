<?php
namespace Database\Seeders;
use App\Models\OperatingUnit;
use Illuminate\Database\Seeder;
class OperatingUnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['code' => 'PMO', 'name' => 'Planning and Management Office', 'type' => 'Office'],
            ['code' => 'CPMD', 'name' => 'Crop Pest Management Division', 'type' => 'Division'],
            ['code' => 'PPD', 'name' => 'Plant Product Development Division', 'type' => 'Division'],
            ['code' => 'NSQCS', 'name' => 'National Seed Quality Control Services', 'type' => 'Division'],
            ['code' => 'NPQSD', 'name' => 'National Plant Quarantine Services Division', 'type' => 'Division'],
            ['code' => 'CRD', 'name' => 'Crop Research and Production Support Division', 'type' => 'Division'],
            ['code' => 'ALGD', 'name' => 'Agricultural Laboratory and Ground Division', 'type' => 'Division'],
        ];
        foreach ($units as $unit) {
            OperatingUnit::firstOrCreate(['code' => $unit['code']], $unit);
        }
    }
}