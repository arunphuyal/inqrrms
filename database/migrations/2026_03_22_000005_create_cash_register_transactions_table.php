<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Manual cash movements logged against an open session (petty cash
     * drops, change-fund top-ups, till-paid expenses). Cash sales are not
     * duplicated here - they're read from Payments at expected-amount
     * calculation time - this table is only for movements that aren't
     * already recorded elsewhere.
     */
    public function up(): void
    {
        Schema::create('cash_register_transactions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('restaurant_id');
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('cash_register_session_id');
            $table->foreign('cash_register_session_id', 'crt_session_fk')->references('id')->on('cash_register_sessions')->onDelete('cascade')->onUpdate('cascade');

            $table->enum('type', ['cash_in', 'cash_out', 'expense']);
            $table->decimal('amount', 16, 2);
            $table->string('reason')->nullable();

            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_register_transactions');
    }
};
