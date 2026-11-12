<?php
//PAPSeeder.php
namespace Database\Seeders;

use App\Models\PAP;
use App\Models\PerformanceIndicator;
use Illuminate\Database\Seeder;

class PAPSeeder extends Seeder
{
    public function run(): void
    {
        $paps = [
            [
                'code' => 'PAP-01',
                'name' => 'Crop Pest Management and Surveillance',
                'category' => 'Operations',
                'indicators' => [
                    ['code' => 'CPMD-IND-01', 'name' => 'Number of infested sites validated', 'unit_of_measure' => 'Sites', 'type' => 'output'],
                    ['code' => 'CPMD-IND-02', 'name' => 'Number of planning workshops conducted', 'unit_of_measure' => 'Sessions', 'type' => 'output'],
                ],
            ],
            [
                'code' => 'PAP-02',
                'name' => 'Plant Product Development & Technology Commercialization',
                'category' => 'Operations',
                'indicators' => [
                    ['code' => 'PPD-IND-01', 'name' => 'Number of processed product technologies developed', 'unit_of_measure' => 'Technologies', 'type' => 'output'],
                ],
            ],
            [
                'code' => 'PAP-03',
                'name' => 'Seed Quality Assurance and Certification',
                'category' => 'Operations',
                'indicators' => [
                    ['code' => 'NSQCS-IND-01', 'name' => 'Number of seed samples analyzed', 'unit_of_measure' => 'Samples', 'type' => 'output'],
                ],
            ],
        ];

        foreach ($paps as $data) {
            $indicators = $data['indicators'];
            unset($data['indicators']);

            $pap = PAP::firstOrCreate(['code' => $data['code']], $data);

            foreach ($indicators as $ind) {
                PerformanceIndicator::firstOrCreate(
                    ['code' => $ind['code']],
                    array_merge($ind, ['pap_id' => $pap->id])
                );
            }
        }
    }
}
