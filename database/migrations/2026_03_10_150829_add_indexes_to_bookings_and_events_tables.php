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
            // Index for payment status filtering (used in widgets, reports, and queries)
            $table->index('payment_status', 'bookings_payment_status_index');
            
            // Index for check-in status filtering (used in ticket scanning)
            $table->index('is_checked_in', 'bookings_is_checked_in_index');
            
            // Composite index for common lookup pattern (code + email)
            $table->index(['code', 'email'], 'bookings_code_email_index');
            
            // Index for event_id foreign key (already indexed by foreign key, but explicit index helps)
            // Note: Laravel automatically indexes foreign keys, but we add composite indexes
            
            // Index for created_at for date-based queries (widgets, reports)
            $table->index('created_at', 'bookings_created_at_index');
        });

        Schema::table('events', function (Blueprint $table) {
            // Index for status filtering (open/closed/ended)
            $table->index('status', 'events_status_index');
            
            // Index for featured events
            $table->index('is_featured', 'events_is_featured_index');
            
            // Index for event date queries
            $table->index('date', 'events_date_index');
            
            // Composite index for category + status (common filter combination)
            $table->index(['category_id', 'status'], 'events_category_status_index');
        });

        Schema::table('venues', function (Blueprint $table) {
            // Index for user_id (venue owner lookup)
            $table->index('user_id', 'venues_user_id_index');
        });

        Schema::table('users', function (Blueprint $table) {
            // Index for role-based queries
            $table->index('role', 'users_role_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_payment_status_index');
            $table->dropIndex('bookings_is_checked_in_index');
            $table->dropIndex('bookings_code_email_index');
            $table->dropIndex('bookings_created_at_index');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex('events_status_index');
            $table->dropIndex('events_is_featured_index');
            $table->dropIndex('events_date_index');
            $table->dropIndex('events_category_status_index');
        });

        Schema::table('venues', function (Blueprint $table) {
            $table->dropIndex('venues_user_id_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_index');
        });
    }
};
