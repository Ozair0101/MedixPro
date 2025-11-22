<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medication_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medication_id')->constrained('medications');
            $table->string('batch_no');
            $table->date('expiry_date');
            $table->integer('quantity')->default(0);
            $table->decimal('cost_price', 10, 2)->default(0);
            $table->decimal('selling_price', 10, 2)->default(0);
            $table->unsignedBigInteger('hospital_id')->index();
            $table->timestamps();

            $table->index(['hospital_id', 'medication_id']);
            $table->index(['hospital_id', 'expiry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medication_batches');
    }
};
