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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('doc_num'); // PO Number
            $table->date('doc_date'); // posting_date
            $table->date('create_date'); // create_date
            $table->date('po_delivery_date');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->date('po_eta')->nullable();
            $table->string('pr_no')->nullable();
            $table->string('unit_no')->nullable();
            $table->string('po_currency');
            $table->decimal('total_po_price', 15, 2);
            $table->decimal('po_with_vat', 15, 2);
            $table->string('project_code');
            $table->string('dept_code');
            $table->string('po_status');
            $table->string('po_delivery_status');
            $table->string('budget_type');
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'revision'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
