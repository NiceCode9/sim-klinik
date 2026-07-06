<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('status')->default('open');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_bills');
    }
};
