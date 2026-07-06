<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('specialization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->date('visit_date');
            $table->string('status')->default('registered');
            $table->string('registration_channel')->default('offline');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
