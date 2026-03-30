<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('po_follows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['purchase_order_id', 'user_id']);
            $table->index('purchase_order_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('po_follows');
    }
};
