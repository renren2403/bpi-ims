<?php
//add_approval_status_to_users.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('designation')->nullable()->after('name');
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('approved')->after('is_active');
            $table->text('approval_remarks')->nullable()->after('approval_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['designation', 'approval_status', 'approval_remarks']);
        });
    }
};
