<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_vitals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('blood_pressure')->nullable();
            $table->string('pulse')->nullable();
            $table->string('temperature')->nullable();
            $table->string('respiration_rate')->nullable();
            $table->decimal('height_cm', 5, 1)->nullable();
            $table->decimal('weight_kg', 5, 1)->nullable();
            $table->text('chief_complaint')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_vitals');
    }
};
