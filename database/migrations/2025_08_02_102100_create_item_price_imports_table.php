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
        Schema::create('item_price_imports', function (Blueprint $table) {
            $table->id();
            $table->integer('supplier_id')->nullable();
            $table->string('item_code')->nullable();
            $table->string('item_description')->nullable();
            $table->string('part_number')->nullable();
            $table->string('brand')->nullable();
            $table->string('project')->nullable();
            $table->string('warehouse')->nullable();
            $table->date('start_date')->nullable();
            $table->date('expired_date')->nullable();
            $table->string('uom')->nullable(); // Unit of Measure
            $table->decimal('qty', 15, 2)->nullable();
            $table->decimal('price', 15, 2)->nullable(); // in IDR
            $table->text('description')->nullable();
            $table->string('import_batch'); // To group imports
            $table->string('status')->default('pending'); // For import validation
            $table->text('error_message')->nullable(); // For import validation errors
            $table->foreignId('uploaded_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_price_imports');
    }
};
