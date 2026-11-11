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
            ['code' => 'AED', 'name' => 'Agricultural Engineering Division', 'type' => 'Division'],
            ['code' => 'ADMIN', 'name' => 'Administrative and Finance Division', 'type' => 'Division'],
            ['code' => 'BNCRDC', 'name' => 'Baguio National Crop Research Development and Production Support Center', 'type' => 'Center'],
            ['code' => 'LNCRDC', 'name' => 'Los Baños National Crop Research Development and Production Support Center', 'type' => 'Center'],
            ['code' => 'DNCRDC', 'name' => 'Davao National Crop Research Development and Production Support Center', 'type' => 'Center'],
            ['code' => 'GNCRDC', 'name' => 'Guimaras National Crop Research Development and Production Support Center', 'type' => 'Center'],
        ];
        foreach ($units as $unit) {
            OperatingUnit::firstOrCreate(['code' => $unit['code']], $unit);
        }
    }
}
