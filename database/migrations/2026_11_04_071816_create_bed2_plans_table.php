<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
       public function up(): void
    {
        Schema::create('bed2_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operating_unit_id')->constrained('operating_units')->cascadeOnDelete();
            $table->foreignId('pap_id')->constrained('paps')->cascadeOnDelete();
            $table->foreignId('performance_indicator_id')->constrained('performance_indicators')->cascadeOnDelete();
            $table->year('year');

            // Annual & Quarterly targets
            $table->decimal('annual_target', 12, 2)->default(0);
            $table->decimal('q1_target', 12, 2)->default(0);
            $table->decimal('q2_target', 12, 2)->default(0);
            $table->decimal('q3_target', 12, 2)->default(0);
            $table->decimal('q4_target', 12, 2)->default(0);

            // Monthly breakdown (m1 to m12)
            for ($m = 1; $m <= 12; $m++) {
                $table->decimal("m{$m}_target", 12, 2)->default(0);
            }

            // Approval workflow
            $table->enum('status', ['draft', 'submitted_to_head', 'head_approved', 'approved', 'returned'])->default('draft');
            $table->text('remarks')->nullable();

            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('head_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pmo_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['operating_unit_id', 'performance_indicator_id', 'year'], 'bed2_ou_indicator_year_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bed2_plans');
    }
};
