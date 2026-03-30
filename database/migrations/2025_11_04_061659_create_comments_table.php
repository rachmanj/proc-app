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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->string('commentable_type');
            $table->unsignedBigInteger('commentable_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('line_item_id')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('content');
            $table->text('content_plain')->nullable();
            $table->boolean('is_resolved')->default(false);
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_deleted')->default(false);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->index(['commentable_type', 'commentable_id'], 'idx_commentable');
            $table->index('parent_id', 'idx_parent');
            $table->index('line_item_id', 'idx_line_item');
            $table->index('user_id', 'idx_user');
            $table->index('is_resolved', 'idx_resolved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
