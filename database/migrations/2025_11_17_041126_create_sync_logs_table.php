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
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('data_type', ['PR', 'PO'])->comment('Type of data synced');
            $table->date('start_date')->comment('Sync start date');
            $table->date('end_date')->comment('Sync end date');
            $table->integer('records_synced')->default(0)->comment('Number of records synced to temp table');
            $table->integer('records_created')->default(0)->comment('Number of records created in main table');
            $table->integer('records_skipped')->default(0)->comment('Number of records skipped (duplicates)');
            $table->enum('sync_status', ['success', 'failed', 'partial'])->default('success');
            $table->enum('convert_status', ['success', 'failed', 'skipped'])->nullable();
            $table->text('error_message')->nullable()->comment('Error message if sync/convert failed');
            $table->unsignedBigInteger('user_id')->nullable()->comment('User who performed the sync');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['data_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
    }
};
