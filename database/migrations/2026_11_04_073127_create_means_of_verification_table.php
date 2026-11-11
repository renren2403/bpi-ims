<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('means_of_verification', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monthly_report_item_id')->constrained('monthly_report_items')->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type')->nullable(); // e.g. "application/pdf"
            $table->unsignedBigInteger('file_size')->nullable(); // in bytes
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('means_of_verification');
    }
};
