<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Purchase Requests Indexes
        Schema::table('purchase_requests', function (Blueprint $table) {
            if (!$this->indexExists('purchase_requests', 'idx_pr_status_date')) {
                $table->index(['pr_status', 'generated_date'], 'idx_pr_status_date');
            }
            if (!$this->indexExists('purchase_requests', 'idx_pr_created_at')) {
                $table->index('created_at', 'idx_pr_created_at');
            }
            if (!$this->indexExists('purchase_requests', 'idx_pr_dept_status')) {
                $table->index(['dept_name', 'pr_status'], 'idx_pr_dept_status');
            }
        });

        // Purchase Orders Indexes
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!$this->indexExists('purchase_orders', 'idx_po_status_date')) {
                $table->index(['status', 'create_date'], 'idx_po_status_date');
            }
            if (!$this->indexExists('purchase_orders', 'idx_po_created_at')) {
                $table->index('created_at', 'idx_po_created_at');
            }
            if (!$this->indexExists('purchase_orders', 'idx_po_supplier_date')) {
                $table->index(['supplier_id', 'doc_date'], 'idx_po_supplier_date');
            }
        });

        // Purchase Order Approvals Indexes
        Schema::table('purchase_order_approvals', function (Blueprint $table) {
            if (!$this->indexExists('purchase_order_approvals', 'idx_poa_status_level')) {
                $table->index(['status', 'approval_level_id'], 'idx_poa_status_level');
            }
            if (!$this->indexExists('purchase_order_approvals', 'idx_poa_created_at')) {
                $table->index('created_at', 'idx_poa_created_at');
            }
            if (!$this->indexExists('purchase_order_approvals', 'idx_poa_approved_at')) {
                $table->index('approved_at', 'idx_poa_approved_at');
            }
        });

        // Purchase Order Details Indexes
        Schema::table('purchase_order_details', function (Blueprint $table) {
            if (!$this->indexExists('purchase_order_details', 'idx_pod_po_id')) {
                $table->index('purchase_order_id', 'idx_pod_po_id');
            }
        });

        // Notifications Indexes
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                if (!$this->indexExists('notifications', 'idx_notifications_created_at')) {
                    $table->index('created_at', 'idx_notifications_created_at');
                }
            });

            // Composite index for read status query optimization
            // Note: MySQL doesn't support partial indexes like PostgreSQL, so we'll create a regular composite index
            if (!$this->indexExists('notifications', 'idx_notifications_user_read')) {
                DB::statement('CREATE INDEX idx_notifications_user_read ON notifications(notifiable_id, read_at)');
            }
        }

        // Comments Indexes
        if (Schema::hasTable('comments')) {
            Schema::table('comments', function (Blueprint $table) {
                if (!$this->indexExists('comments', 'idx_comments_commentable')) {
                    $table->index(['commentable_type', 'commentable_id'], 'idx_comments_commentable');
                }
                if (!$this->indexExists('comments', 'idx_comments_line_item')) {
                    $table->index('line_item_id', 'idx_comments_line_item');
                }
            });
        }
    }

    public function down()
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropIndex('idx_pr_status_date');
            $table->dropIndex('idx_pr_created_at');
            $table->dropIndex('idx_pr_dept_status');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropIndex('idx_po_status_date');
            $table->dropIndex('idx_po_created_at');
            $table->dropIndex('idx_po_supplier_date');
        });

        Schema::table('purchase_order_approvals', function (Blueprint $table) {
            $table->dropIndex('idx_poa_status_level');
            $table->dropIndex('idx_poa_created_at');
            $table->dropIndex('idx_poa_approved_at');
        });

        Schema::table('purchase_order_details', function (Blueprint $table) {
            $table->dropIndex('idx_pod_po_id');
        });

        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropIndex('idx_notifications_created_at');
            });
            DB::statement('DROP INDEX IF EXISTS idx_notifications_user_read ON notifications');
        }

        if (Schema::hasTable('comments')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->dropIndex('idx_comments_commentable');
                $table->dropIndex('idx_comments_line_item');
            });
        }
    }

    private function indexExists($table, $index)
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();
        $result = $connection->select(
            "SELECT COUNT(*) as count FROM information_schema.statistics 
             WHERE table_schema = ? AND table_name = ? AND index_name = ?",
            [$database, $table, $index]
        );
        return $result[0]->count > 0;
    }
};
