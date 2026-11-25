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
            $table->text('sub_program')->nullable()->after('pap_id');
            $table->text('activity')->nullable()->after('sub_program');
            $table->text('sub_activity')->nullable()->after('activity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('performance_indicators', function (Blueprint $table) {
            $table->dropColumn(['sub_program', 'activity', 'sub_activity']);
        });
    }
};
