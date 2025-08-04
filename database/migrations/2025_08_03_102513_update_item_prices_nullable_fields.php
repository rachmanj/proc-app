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
        Schema::table('item_prices', function (Blueprint $table) {
            $table->string('item_code')->nullable()->change();
            $table->string('item_description')->nullable()->change();
            $table->string('project')->nullable()->change();
            $table->string('warehouse')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_prices', function (Blueprint $table) {
            $table->string('item_code')->nullable(false)->change();
            $table->string('item_description')->nullable(false)->change();
            $table->string('project')->nullable(false)->change();
            $table->string('warehouse')->nullable(false)->change();
        });
    }
};
