<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Audit trail of every bill / credit-note submission attempted against
     * IRD's CBMS, so a failed or pending sync can be spotted and retried,
     * and so there is a record of exactly what was sent for compliance
     * purposes.
     */
    public function up(): void
    {
        Schema::create('cbms_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('restaurant_id');
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null')->onUpdate('cascade');

            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade')->onUpdate('cascade');

            $table->enum('type', ['bill', 'credit_note'])->default('bill');
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');

            // Raw CBMS response code (e.g. "200", "101") kept as a string
            // since the API returns it as a bare value rather than a typed field.
            $table->string('response_code')->nullable();
            $table->text('response_message')->nullable();

            $table->json('request_payload')->nullable();
            $table->unsignedInteger('attempts')->default(0);
            $table->dateTime('submitted_at')->nullable();

            $table->timestamps();

            $table->index(['order_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cbms_logs');
    }
};
