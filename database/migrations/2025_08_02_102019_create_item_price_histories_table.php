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
        Schema::create('item_price_histories', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->nullable();
            $table->string('item_description')->nullable();
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->string('project')->nullable();
            $table->string('warehouse')->nullable();
            $table->string('part_number')->nullable();
            $table->string('brand')->nullable();
            $table->decimal('price', 15, 2); // in IDR
            $table->string('uom'); // Unit of Measure
            $table->decimal('qty', 15, 2);
            $table->date('start_date');
            $table->date('expired_date')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            // Create indexes for frequently searched columns
            $table->index('item_code');
            $table->index('item_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_price_histories');
    }
};
