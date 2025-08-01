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
        Schema::create('item_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('po_service_id')->nullable();
            $table->string('item_code');
            $table->string('item_desc');
            $table->string('uom');
            $table->integer('qty');
            $table->double('unit_price');
            $table->string('flag', 20)->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_services');
    }
};
