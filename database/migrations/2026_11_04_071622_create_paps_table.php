<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paps', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();         // e.g. "PAP-310100100001000"
            $table->string('name');                   // e.g. "Crop Research and Development Project"
            $table->text('description')->nullable();
            $table->string('category')->nullable();   // e.g. "Operations", "Support to Operations", "GASS"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paps');
    }
};
