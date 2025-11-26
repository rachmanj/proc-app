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
        Schema::table('pr_temps', function (Blueprint $table) {
            $table->integer('sap_doc_entry')->nullable()->after('id')->comment('SAP OPRQ.DocEntry');
            $table->integer('sap_line_num')->nullable()->after('sap_doc_entry')->comment('SAP PRQ1.LineNum');
            $table->integer('sap_vis_order')->nullable()->after('sap_line_num')->comment('SAP PRQ1.VisOrder');
            $table->string('line_identity', 64)->nullable()->after('sap_vis_order')->comment('Hash for deduplication when SAP IDs unavailable');
        });

        Schema::table('purchase_request_details', function (Blueprint $table) {
            $table->integer('sap_doc_entry')->nullable()->after('purchase_request_id')->comment('SAP OPRQ.DocEntry');
            $table->integer('sap_line_num')->nullable()->after('sap_doc_entry')->comment('SAP PRQ1.LineNum');
            $table->integer('sap_vis_order')->nullable()->after('sap_line_num')->comment('SAP PRQ1.VisOrder');
            $table->string('line_identity', 64)->nullable()->after('sap_vis_order')->comment('Hash for deduplication when SAP IDs unavailable');
            
            $table->unique(['purchase_request_id', 'sap_doc_entry', 'sap_line_num'], 'pr_detail_sap_identity_unique');
            $table->unique(['purchase_request_id', 'line_identity'], 'pr_detail_line_identity_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_request_details', function (Blueprint $table) {
            $table->dropUnique('pr_detail_sap_identity_unique');
            $table->dropUnique('pr_detail_line_identity_unique');
            $table->dropColumn(['sap_doc_entry', 'sap_line_num', 'sap_vis_order', 'line_identity']);
        });

        Schema::table('pr_temps', function (Blueprint $table) {
            $table->dropColumn(['sap_doc_entry', 'sap_line_num', 'sap_vis_order', 'line_identity']);
        });
    }
};
