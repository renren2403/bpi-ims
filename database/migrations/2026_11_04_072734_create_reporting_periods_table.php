<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('reporting_periods', function (Blueprint $table)
        {
        $table->id();
        $table->year('year');
        $table->unsignedTinyInteger('month');
        $table->string('name');
        $table->dateTime('window_start');
        $table->dateTime('window_end');
        $table->boolean('is_active')->default(false);
        $table->enum('status', ['upcoming','open','closed'])->default('upcoming');
        $table->text('remarks')->nullable();
        $table->timestamps();
        $table->unique(['year','month']);

        });
        }

        public function down(): void 
        {
            Schema::dropIfExists('reporting_periods');
        }

};
