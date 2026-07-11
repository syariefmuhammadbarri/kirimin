<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number')->unique();
            $table->string('booking_code')->unique();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->foreignId('courier_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('set null');
            
            $table->string('status')->default('booking_created'); 
            // booking_created, waiting_dropoff, weighed, payment_pending, payment_confirmed, received_at_branch, assigned_to_courier, out_for_delivery, delivered, gagal_kirim, cancelled
            
            $table->string('origin_city');
            $table->string('destination_city');
            $table->string('sender_name');
            $table->string('sender_phone');
            $table->text('sender_address');
            $table->string('receiver_name');
            $table->string('receiver_phone');
            $table->text('receiver_address');
            
            $table->decimal('estimated_weight', 8, 2);
            $table->decimal('actual_weight', 8, 2)->nullable();
            $table->decimal('estimated_price', 12, 2);
            $table->decimal('actual_price', 12, 2)->nullable();
            $table->decimal('total_price', 12, 2);
            $table->string('service_type')->default('regular'); // regular, express
            
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('shipments');
    }
};
