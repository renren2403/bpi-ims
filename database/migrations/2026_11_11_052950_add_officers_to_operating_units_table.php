<?php
//add_officers_to_operating_units_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operating_units', function (Blueprint $table) {
        $table->foreignId('division_chief_id')
        ->nullable()
        ->after('description')
        ->constrained('users')
        ->nullOnDelete();

        $table->foreignId('reporting_officer_id')
        ->nullable()
        ->after('division_chief_id')
        ->constrained('users')
        ->nullOnDelete();
        });
    }
    public function down(): void
    {
        Schema::table('operating_units', function (Blueprint $table) {
            $table->dropForeign(['division_chief_id']);
            $table->dropForeign(['reporting_officer_id']);
            $table->dropColumn(['division_chief_id','reporting_officer_id']);
        });
    }
};
