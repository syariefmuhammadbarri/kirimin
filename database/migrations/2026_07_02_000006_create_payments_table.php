<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', ['cash', 'transfer', 'e-wallet', 'midtrans'])->default('transfer');
            $table->string('payment_status')->default('pending'); // pending, paid, failed
            $table->string('snap_token')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('payments');
    }
};
