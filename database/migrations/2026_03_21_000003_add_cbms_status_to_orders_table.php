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
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('cbms_status', ['not_applicable', 'pending', 'synced', 'failed'])
                ->default('not_applicable')
                ->after('status');
            $table->dateTime('cbms_synced_at')->nullable()->after('cbms_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['cbms_status', 'cbms_synced_at']);
        });
    }
};
