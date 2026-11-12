<?php
//monthly_reports_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operating_unit_id')->constrained('operating_units')->cascadeOnDelete();
            $table->foreignId('reporting_period_id')->constrained('reporting_periods')->cascadeOnDelete();
            $table->year('year');
            $table->unsignedTinyInteger('month');
            $table->enum('status', ['draft', 'submitted', 'head_approved', 'evaluated', 'returned'])->default('draft');
            $table->text('remarks')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('head_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('head_approved_at')->nullable();
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('evaluated_at')->nullable();
            $table->timestamps();
            $table->unique(['operating_unit_id', 'reporting_period_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_reports');
    }
};
