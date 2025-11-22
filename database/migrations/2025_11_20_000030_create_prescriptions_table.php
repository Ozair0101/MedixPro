<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('doctor_id');
            $table->string('op_ip_type')->comment('OPD, IPD, ER');
            $table->string('status')->default('Pending');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('hospital_id')->index();
            $table->timestamps();

            $table->index(['hospital_id', 'patient_id']);
            $table->index(['hospital_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
