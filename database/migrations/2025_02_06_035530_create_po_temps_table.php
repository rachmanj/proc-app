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
        Schema::create('po_temps', function (Blueprint $table) {
            $table->id();
            $table->string('po_no');
            $table->date('posting_date');
            $table->date('create_date');
            $table->date('po_delivery_date');
            $table->date('po_eta');
            $table->string('pr_no');
            $table->string('vendor_code');
            $table->string('vendor_name');
            $table->string('unit_no');
            $table->string('item_code');
            $table->text('description');
            $table->integer('qty');
            $table->string('po_currency');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('item_amount', 15, 2);
            $table->decimal('total_po_price', 15, 2);
            $table->decimal('po_with_vat', 15, 2);
            $table->string('uom')->nullable();
            $table->string('project_code');
            $table->string('dept_code');
            $table->string('po_status');
            $table->string('po_delivery_status');
            $table->string('budget_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('po_temps');
    }
};
