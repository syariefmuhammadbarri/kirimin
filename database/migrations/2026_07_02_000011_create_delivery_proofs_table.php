<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('delivery_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->onDelete('cascade');
            $table->foreignId('courier_id')->constrained('users')->onDelete('cascade');
            $table->text('photos'); // JSON array of photo paths
            $table->text('notes')->nullable();
            $table->string('recipient_name');
            $table->longText('recipient_signature'); // Base64 signature path or data
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('delivery_proofs');
    }
};
