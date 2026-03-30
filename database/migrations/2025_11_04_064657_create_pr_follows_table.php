<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pr_follows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')->constrained('purchase_requests')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['purchase_request_id', 'user_id']);
            $table->index('purchase_request_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pr_follows');
    }
};
