<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('unlock_requests', function (Blueprint $table) {
        $table->id();
        $table->foreignId('monthly_report_id')->constrained('monthly_reports')->cascadeOnDelete();
        $table->foreignId('operating_unit_id')->constrained('operating_units')->cascadeOnDelete();
        $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
        $table->text('reason');
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        
        $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamp('reviewed_at')->nullable();
        $table->text('review_remarks')->nullable();
        $table->dateTime('expires_at')->nullable(); 
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('unlock_requests');
}
};
