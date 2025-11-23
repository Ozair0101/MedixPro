<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('patient_code', 20)->unique();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->date('date_of_birth');
            $table->string('gender', 20);
            $table->enum('blood_type', ['A+','A-','B+','B-','AB+','AB-','O+','O-'])->nullable();
            $table->string('marital_status', 20)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone_number', 20);
            $table->string('emergency_contact_phone', 20);
            $table->jsonb('address_json')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Add indexes
            $table->index(['first_name', 'last_name'], 'idx_patients_name');
            $table->index('date_of_birth', 'idx_patients_dob');
            $table->index('phone_number', 'idx_patients_phone');
        });
    }

    public function down()
    {
        Schema::dropIfExists('patients');
    }
};
