<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_proofs', function (Blueprint $table) {
            $table->enum('admin_status', ['pending', 'accepted', 'rejected'])->default('pending')->after('notes');
            $table->text('admin_notes')->nullable()->after('admin_status');
            $table->unsignedBigInteger('reviewed_by')->nullable()->after('admin_notes');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_proofs', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['admin_status', 'admin_notes', 'reviewed_by', 'reviewed_at']);
        });
    }
};