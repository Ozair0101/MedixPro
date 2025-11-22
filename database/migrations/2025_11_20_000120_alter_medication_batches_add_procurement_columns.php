<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medication_batches', function (Blueprint $table) {
            $table->unsignedBigInteger('supplier_id')->nullable()->after('selling_price');
            $table->unsignedBigInteger('purchase_id')->nullable()->after('supplier_id');
            $table->index(['hospital_id', 'supplier_id']);
        });
    }

    public function down(): void
    {
        Schema::table('medication_batches', function (Blueprint $table) {
            $table->dropIndex(['hospital_id', 'supplier_id']);
            $table->dropColumn(['supplier_id', 'purchase_id']);
        });
    }
};
