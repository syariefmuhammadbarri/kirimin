<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->timestamp('cancelled_at')->nullable()->after('pickup_notes');
            $table->string('cancel_reason', 255)->nullable()->after('cancelled_at');
            $table->tinyInteger('delivery_attempt_count')->unsigned()->default(0)->after('cancel_reason');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['cancelled_at', 'cancel_reason', 'delivery_attempt_count']);
        });
    }
};
