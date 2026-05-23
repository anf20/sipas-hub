<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fee_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', ['SPP', 'kegiatan', 'seragam', 'lain']);
            $table->decimal('default_amount', 15, 2);
            $table->boolean('is_recurring')->default(false);
            $table->enum('recurrence', ['bulanan', 'tahunan', 'sekali'])->nullable();
            $table->json('applicable_grades')->nullable(); // e.g., ["7", "8", "9"]
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_types');
    }
};
