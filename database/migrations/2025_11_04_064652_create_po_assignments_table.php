<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('po_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
            $table->foreignId('assigned_to_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('purchase_order_id');
            $table->index('assigned_to_user_id');
            $table->unique(['purchase_order_id', 'assigned_to_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('po_assignments');
    }
};
