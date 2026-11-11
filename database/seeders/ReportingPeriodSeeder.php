<?php

namespace Database\Seeders;

use App\Models\ReportingPeriod;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ReportingPeriodSeeder extends Seeder
{
    /**
     * Seed an official active reporting period.
     */
    public function run(): void
    {
        $year = (int) date('Y');
        $month = (int) date('n');

        // Create or update an active reporting cycle for the current calendar period
        ReportingPeriod::updateOrCreate(
            ['year' => $year, 'month' => $month],
            [
                'name' => Carbon::create($year, $month)->format('F Y').' Monthly Physical Accomplishment',
                'window_start' => Carbon::create($year, $month, 21, 0, 0, 0),
                'window_end' => Carbon::create($year, $month, 26, 23, 59, 59),
                'is_active' => true,
                'status' => 'open',
                'remarks' => 'Official monthly BED-2 accomplishment submission window set by PMO Administrator.',
            ]
        );
    }
}
