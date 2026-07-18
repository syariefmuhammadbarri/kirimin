<?php

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
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('fulfillment_type')->default('dropoff')->after('service_type');
            $table->text('pickup_address')->nullable()->after('fulfillment_type');
            $table->dateTime('pickup_scheduled_at')->nullable()->after('pickup_address');
            $table->text('pickup_notes')->nullable()->after('pickup_scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropColumn(['fulfillment_type', 'pickup_address', 'pickup_scheduled_at', 'pickup_notes']);
        });
    }
};
