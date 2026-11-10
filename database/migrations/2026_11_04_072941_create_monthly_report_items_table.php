<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
         Schema::create('monthly_report_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('monthly_report_id')->constrained('monthly_reports')->cascadeOnDelete();
        $table->foreignId('bed2_plan_id')->constrained('bed2_plans')->cascadeOnDelete();
        $table->foreignId('performance_indicator_id')->constrained('performance_indicators')->cascadeOnDelete();
        $table->decimal('target', 12, 2)->default(0);
        $table->decimal('actual', 12, 2)->default(0);
        $table->decimal('variance', 12, 2)->default(0); 
        $table->decimal('slippage_rate', 8, 2)->default(0); 
        $table->enum('status', ['on_track', 'caution', 'critical_slippage'])->default('on_track');
        $table->text('root_cause')->nullable();
        $table->text('immediate_correction')->nullable();
        $table->text('preventative_action')->nullable();
        $table->text('remarks')->nullable();
        $table->timestamps();
        $table->unique(['monthly_report_id', 'performance_indicator_id'], 'mri_report_indicator_unique');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('monthly_report_items');
    }
};
