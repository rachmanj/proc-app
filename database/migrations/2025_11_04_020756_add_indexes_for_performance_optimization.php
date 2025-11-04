<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            if (!$this->hasIndex('purchase_requests', 'idx_pr_status_date')) {
                $table->index(['pr_status', 'generated_date'], 'idx_pr_status_date');
            }
            if (!$this->hasIndex('purchase_requests', 'purchase_requests_pr_no_index')) {
                $table->index('pr_no');
            }
            if (!$this->hasIndex('purchase_requests', 'purchase_requests_dept_name_index')) {
                $table->index('dept_name');
            }
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!$this->hasIndex('purchase_orders', 'idx_po_status_date')) {
                $table->index(['status', 'create_date'], 'idx_po_status_date');
            }
            if (!$this->hasIndex('purchase_orders', 'purchase_orders_doc_num_index')) {
                $table->index('doc_num');
            }
            if (!$this->hasIndex('purchase_orders', 'purchase_orders_pr_no_index')) {
                $table->index('pr_no');
            }
        });

        Schema::table('purchase_order_approvals', function (Blueprint $table) {
            if (!$this->hasIndex('purchase_order_approvals', 'idx_approval_level_status')) {
                $table->index(['approval_level_id', 'status'], 'idx_approval_level_status');
            }
            if (!$this->hasIndex('purchase_order_approvals', 'purchase_order_approvals_purchase_order_id_index')) {
                $table->index('purchase_order_id');
            }
            if (!$this->hasIndex('purchase_order_approvals', 'purchase_order_approvals_status_index')) {
                $table->index('status');
            }
        });

        Schema::table('item_prices', function (Blueprint $table) {
            if (!$this->hasIndex('item_prices', 'item_prices_supplier_id_index')) {
                $table->index('supplier_id');
            }
            if (!$this->hasIndex('item_prices', 'item_prices_item_code_index')) {
                $table->index('item_code');
            }
        });
    }

    private function hasIndex($table, $indexName): bool
    {
        $connection = Schema::getConnection();
        $databaseName = $connection->getDatabaseName();
        $indexes = $connection->select("SHOW INDEXES FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        return count($indexes) > 0;
    }

    public function down(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropIndex('idx_pr_status_date');
            $table->dropIndex(['pr_no']);
            $table->dropIndex(['dept_name']);
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropIndex('idx_po_status_date');
            $table->dropIndex(['doc_num']);
            $table->dropIndex(['pr_no']);
        });

        Schema::table('purchase_order_approvals', function (Blueprint $table) {
            $table->dropIndex('idx_approval_level_status');
            $table->dropIndex(['purchase_order_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('item_prices', function (Blueprint $table) {
            $table->dropIndex(['supplier_id']);
            $table->dropIndex(['item_code']);
        });
    }
};
