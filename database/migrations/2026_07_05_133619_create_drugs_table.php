<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drugs', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->string('category')->nullable();
            $table->string('unit');
            $table->boolean('is_fractional')->default(false);
            $table->enum('pricing_type', ['margin_percentage', 'flat']);
            $table->decimal('price_value', 15, 2);
            $table->decimal('minimum_stock', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drugs');
    }
};
