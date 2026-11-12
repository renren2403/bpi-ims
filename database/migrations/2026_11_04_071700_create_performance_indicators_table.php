<?php
//create_performance_indicators_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pap_id')->constrained('paps')->cascadeOnDelete();
            $table->string('code')->nullable();              // e.g. "OO-1.1", "OC-2"
            $table->text('name');                             // e.g. "Number of seed samples tested"
            $table->string('unit_of_measure');               // e.g. "samples", "hectares", "farmers"
            $table->enum('type', ['output', 'outcome'])->default('output');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_indicators');
    }
};
