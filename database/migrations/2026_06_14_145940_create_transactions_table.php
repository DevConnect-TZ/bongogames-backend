<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('game_id')->constrained('games')->cascadeOnDelete();
            $table->string('order_id')->unique();
            $table->string('reference')->nullable();
            $table->string('buyer_phone');
            $table->unsignedInteger('amount');
            $table->string('currency')->default('TZS');
            $table->string('gateway')->default('mobilipa');
            $table->string('payment_status')->default('PENDING');
            $table->string('transaction_id')->nullable();
            $table->string('channel')->nullable();
            $table->string('msisdn')->nullable();
            $table->json('response_data')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
