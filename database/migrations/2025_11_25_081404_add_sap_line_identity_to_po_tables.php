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
        Schema::table('po_temps', function (Blueprint $table) {
            $table->unsignedBigInteger('sap_doc_entry')->nullable()->after('budget_type');
            $table->unsignedInteger('sap_line_num')->nullable()->after('sap_doc_entry');
            $table->unsignedInteger('sap_vis_order')->nullable()->after('sap_line_num');
        });

        Schema::table('purchase_order_details', function (Blueprint $table) {
            $table->unsignedBigInteger('sap_doc_entry')->nullable()->after('item_amount');
            $table->unsignedInteger('sap_line_num')->nullable()->after('sap_doc_entry');
            $table->unsignedInteger('sap_vis_order')->nullable()->after('sap_line_num');
            $table->string('line_identity')->nullable()->after('sap_vis_order');

            $table->unique(
                ['purchase_order_id', 'sap_doc_entry', 'sap_line_num'],
                'po_detail_sap_line_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('po_temps', function (Blueprint $table) {
            $table->dropColumn(['sap_doc_entry', 'sap_line_num', 'sap_vis_order']);
        });

        Schema::table('purchase_order_details', function (Blueprint $table) {
            $table->dropUnique('po_detail_sap_line_unique');
            $table->dropColumn(['sap_doc_entry', 'sap_line_num', 'sap_vis_order', 'line_identity']);
        });
    }
};
