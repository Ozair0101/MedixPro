<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('sale_no')->unique();
            $table->unsignedBigInteger('patient_id')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->string('sale_type')->default('cash'); // cash, insurance, credit
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('status')->default('pending'); // pending, paid, cancelled, returned
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('hospital_id')->index();
            $table->timestamps();

            $table->index(['hospital_id', 'sale_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
