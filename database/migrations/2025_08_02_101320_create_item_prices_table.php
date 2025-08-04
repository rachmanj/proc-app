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
        Schema::create('item_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->string('item_code')->nullable();
            $table->string('item_description')->nullable();
            $table->string('part_number')->nullable();
            $table->string('brand')->nullable();
            $table->string('project')->nullable();
            $table->string('warehouse')->nullable();
            $table->date('start_date');
            $table->date('expired_date')->nullable();
            $table->foreignId('uploaded_by')->constrained('users');
            $table->string('uom'); // Unit of Measure
            $table->decimal('qty', 15, 2);
            $table->decimal('price', 15, 2); // in IDR
            $table->text('description')->nullable();
            $table->timestamps();

            // Create indexes for frequently searched columns
            $table->index('item_code');
            $table->index('item_description');
            $table->index('project');
            $table->index('warehouse');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_prices');
    }
};
