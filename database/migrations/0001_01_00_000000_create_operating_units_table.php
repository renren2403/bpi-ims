<?php
//create_operating_units_table.php
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
        Schema::create('operating_units', function (Blueprint $table) {
            $table->id();
            $table->string('name');                      // e.g. "Crop Research and Development Division"
            $table->string('code')->unique();            // e.g. "CRDD", "NSIC", "BPI-CENTRAL"
            $table->string('type')->nullable();          // e.g. "Division", "Center", "Station"
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operating_units');
    }
};
