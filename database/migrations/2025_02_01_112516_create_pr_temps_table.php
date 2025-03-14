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
        Schema::create('pr_temps', function (Blueprint $table) {
            $table->id();
            $table->string('pr_draft_no', 30)->nullable();
            $table->string('pr_no', 30)->nullable();
            $table->date('pr_date')->nullable();
            $table->date('generated_date')->nullable();
            $table->string('priority', 30)->nullable();
            $table->string('pr_status', 30)->nullable();
            $table->string('closed_status', 30)->nullable();
            $table->string('pr_rev_no', 30)->nullable();
            $table->string('pr_type')->nullable();
            $table->string('project_code', 30)->nullable();
            $table->string('dept_name')->nullable();
            $table->string('for_unit')->nullable();
            $table->integer('hours_meter')->nullable();
            $table->date('required_date')->nullable();
            $table->string('requestor')->nullable();
            $table->string('item_code')->nullable();
            $table->string('item_name')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('uom', 30)->nullable();
            $table->integer('open_qty')->nullable();
            $table->text('line_remarks')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pr_temps');
    }
};
