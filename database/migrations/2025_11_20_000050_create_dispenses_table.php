<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_item_id')->constrained('prescription_items')->onDelete('cascade');
            $table->foreignId('medication_id')->constrained('medications');
            $table->foreignId('batch_id')->constrained('medication_batches');
            $table->integer('quantity');
            $table->unsignedBigInteger('pharmacist_id');
            $table->timestamp('dispense_time')->useCurrent();
            $table->unsignedBigInteger('hospital_id')->index();
            $table->timestamps();

            $table->index(['hospital_id', 'dispense_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispenses');
    }
};
