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
        Schema::create('comment_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')->constrained('comments')->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path', 500);
            $table->string('file_type', 100);
            $table->unsignedBigInteger('file_size');
            $table->string('mime_type', 100);
            $table->timestamps();

            $table->index('comment_id', 'idx_comment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_attachments');
    }
};
