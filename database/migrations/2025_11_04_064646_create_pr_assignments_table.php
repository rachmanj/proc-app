<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pr_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')->constrained('purchase_requests')->onDelete('cascade');
            $table->foreignId('assigned_to_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('purchase_request_id');
            $table->index('assigned_to_user_id');
            $table->unique(['purchase_request_id', 'assigned_to_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pr_assignments');
    }
};
