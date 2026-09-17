<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Currency note/coin denominations a restaurant counts cash in, used on
     * the Denominations settings screen and when counting an opening/closing
     * cash register drawer.
     */
    public function up(): void
    {
        Schema::create('cash_register_denominations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('restaurant_id');
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade')->onUpdate('cascade');

            $table->decimal('value', 16, 2);
            $table->enum('type', ['note', 'coin'])->default('note');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_register_denominations');
    }
};
