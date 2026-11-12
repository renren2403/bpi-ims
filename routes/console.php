<?php
//console.php
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:reset {password=password123}', function ($password = 'password123') {
    $user = \App\Models\User::firstOrNew(['email' => 'admin@bpi.gov.ph']);
    $user->name = 'PMO Administrator';
    $user->password = $password;
    $user->approval_status = 'approved';
    $user->is_active = true;
    $user->save();
    $user->syncRoles(['superadmin', 'admin']);

    $this->info("✓ Superadmin account 'admin@bpi.gov.ph' password reset to: {$password}");
})->purpose('Reset or create the superadmin user credentials');
