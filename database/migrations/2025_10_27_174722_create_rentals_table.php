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
        Schema::create('rentals', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('unit_id')->constrained()->onDelete('cascade');
        $table->timestamp('rent_date');
        $table->timestamp('due_date');
        $table->timestamp('return_date')->nullable();
        $table->enum('status', ['rented', 'returned', 'overdue'])->default('rented');
        $table->decimal('fine_amount', 10, 2)->default(0);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
