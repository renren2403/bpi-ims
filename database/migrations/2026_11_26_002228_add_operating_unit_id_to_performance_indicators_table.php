<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('performance_indicators', function (Blueprint $table) {
            $table->foreignId('operating_unit_id')->nullable()->after('pap_id')->constrained('operating_units')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('performance_indicators', function (Blueprint $table) {
            $table->dropForeign(['operating_unit_id']);
            $table->dropColumn('operating_unit_id');
        });
    }
};
