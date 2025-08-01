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
        // First drop the pivot table since it has foreign key constraints
        Schema::dropIfExists('pr_attachment_purchase_request');
        
        // Drop and recreate pr_attachments table with correct structure
        Schema::dropIfExists('pr_attachments');
        
        Schema::create('pr_attachments', function (Blueprint $table) {
            $table->id();
            $table->string('original_name');
            $table->string('file_path');
            $table->text('description')->nullable();
            $table->string('keterangan')->nullable();
            $table->string('pr_no')->nullable();
            $table->string('file_type')->nullable();
            $table->integer('file_size')->nullable();
            $table->timestamps();
        });

        // Recreate pivot table
        Schema::create('pr_attachment_purchase_request', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_request_id')->constrained('purchase_requests');
            $table->foreignId('pr_attachment_id')->constrained('pr_attachments');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pr_attachment_purchase_request');
        Schema::dropIfExists('pr_attachments');
    }
}; 