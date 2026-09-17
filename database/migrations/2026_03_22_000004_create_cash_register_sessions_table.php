<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One open->close cycle of a cash register. expected_closing_amount is a
     * system estimate (opening float + cash sales + manual cash-in - manual
     * cash-out, computed at close time); counted_closing_amount is what the
     * cashier actually counted. status moves open -> pending_approval (if a
     * discrepancy or the restaurant's settings require it) -> approved /
     * rejected, or straight open -> closed when no approval is needed.
     */
    public function up(): void
    {
        Schema::create('cash_register_sessions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('restaurant_id');
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('cash_register_id');
            $table->foreign('cash_register_id')->references('id')->on('cash_registers')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('opened_by');
            $table->foreign('opened_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->dateTime('opened_at');
            $table->decimal('opening_amount', 16, 2)->default(0);

            $table->unsignedBigInteger('closed_by')->nullable();
            $table->foreign('closed_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            $table->dateTime('closed_at')->nullable();
            $table->decimal('expected_closing_amount', 16, 2)->nullable();
            $table->decimal('counted_closing_amount', 16, 2)->nullable();
            $table->decimal('difference', 16, 2)->nullable();

            $table->enum('status', ['open', 'pending_approval', 'approved', 'rejected', 'closed'])->default('open');

            $table->unsignedBigInteger('approved_by')->nullable();
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            $table->dateTime('approved_at')->nullable();
            $table->text('approval_note')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['branch_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_register_sessions');
    }
};
