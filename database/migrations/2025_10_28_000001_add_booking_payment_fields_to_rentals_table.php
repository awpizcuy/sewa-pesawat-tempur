<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->string('booking_code')->nullable()->after('id');
            $table->string('borrower_name')->nullable()->after('user_id');
            $table->string('borrower_identity_number')->nullable()->after('borrower_name');
            $table->enum('payment_method', ['ewallet','transfer','va'])->nullable()->after('status');
            $table->decimal('total_amount', 12, 2)->default(0)->after('fine_amount');
        });
    }

    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn(['booking_code','borrower_name','borrower_identity_number','payment_method','total_amount']);
        });
    }
};


