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
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('promo_code')->nullable()->after('total');
            $table->decimal('discount', 12, 2)->default(0)->after('promo_code');
            $table->decimal('final_total', 12, 2)->after('discount');
            
            // Index for promo code queries
            $table->index('promo_code', 'bookings_promo_code_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_promo_code_index');
            $table->dropColumn(['promo_code', 'discount', 'final_total']);
        });
    }
};
