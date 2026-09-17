<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One settings row per restaurant controlling how register close-out
     * approvals are triggered.
     */
    public function up(): void
    {
        Schema::create('cash_register_settings', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('restaurant_id')->unique();
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade')->onUpdate('cascade');

            $table->boolean('require_denomination_count')->default(true);
            $table->boolean('require_approval_on_discrepancy')->default(true);
            $table->decimal('discrepancy_threshold', 16, 2)->default(0);
            $table->boolean('always_require_approval')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_register_settings');
    }
};
