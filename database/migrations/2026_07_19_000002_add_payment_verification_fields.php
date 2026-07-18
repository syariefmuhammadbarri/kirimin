<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Nominal uang yang benar-benar diterima kasir (verifikasi cash)
            $table->decimal('paid_amount', 12, 2)->nullable()->after('snap_token');
            // Batas waktu pembayaran setelah booking dibuat
            $table->timestamp('expired_at')->nullable()->after('paid_amount');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['paid_amount', 'expired_at']);
        });
    }
};
