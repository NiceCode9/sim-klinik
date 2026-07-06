<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained()->cascadeOnDelete();
            $table->integer('queue_number');
            $table->foreignId('specialization_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('waiting');
            $table->string('source')->default('offline');
            $table->timestamp('called_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
