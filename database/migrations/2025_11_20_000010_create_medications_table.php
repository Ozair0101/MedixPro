<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('generic_name')->nullable();
            $table->string('strength')->nullable();
            $table->string('form')->comment('tablet, injection, syrup, etc.');
            $table->decimal('standard_dose', 8, 2)->nullable();
            $table->boolean('controlled')->default(false);
            $table->integer('min_stock')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->unsignedBigInteger('hospital_id')->index();
            $table->timestamps();

            $table->index(['hospital_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};
