<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_test_masters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', ['lab', 'radiology']);
            $table->string('unit')->nullable();
            $table->decimal('normal_range_min', 15, 2)->nullable();
            $table->decimal('normal_range_max', 15, 2)->nullable();
            $table->foreignId('tariff_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_test_masters');
    }
};
