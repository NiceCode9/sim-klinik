<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procedure_tariffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('icd9cm_code_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->decimal('amount', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procedure_tariffs');
    }
};
