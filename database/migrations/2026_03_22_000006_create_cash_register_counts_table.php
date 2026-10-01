<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Denomination-by-denomination breakdown of an opening or closing cash
     * count for a session, when the restaurant requires a full count
     * (see cash_register_settings.require_denomination_count).
     */
    public function up(): void
    {
        Schema::create('cash_register_counts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('cash_register_session_id');
            $table->foreign('cash_register_session_id', 'crc_session_fk')->references('id')->on('cash_register_sessions')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('cash_register_denomination_id');
            $table->foreign('cash_register_denomination_id', 'crc_denomination_fk')->references('id')->on('cash_register_denominations')->onDelete('cascade')->onUpdate('cascade');

            $table->enum('stage', ['opening', 'closing']);
            $table->unsignedInteger('quantity')->default(0);
            $table->decimal('subtotal', 16, 2)->default(0);

            $table->timestamps();

            $table->unique(['cash_register_session_id', 'cash_register_denomination_id', 'stage'], 'crc_session_denom_stage_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_register_counts');
    }
};
