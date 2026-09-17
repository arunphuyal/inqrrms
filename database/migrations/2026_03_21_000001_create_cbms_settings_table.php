<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * One credential set per restaurant for IRD's Central Billing Monitoring
     * System (CBMS). username/password are the Taxpayer Portal login used to
     * authenticate every /api/bill and /api/billreturn call (per the CBMS API
     * documentation, these are sent in the JSON body, not as HTTP headers).
     */
    public function up(): void
    {
        Schema::create('cbms_settings', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('restaurant_id')->unique();
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade')->onUpdate('cascade');

            $table->boolean('is_enabled')->default(false);
            $table->enum('mode', ['test', 'live'])->default('test');

            $table->string('username')->nullable();
            $table->text('password')->nullable();

            // Overrides the seller PAN normally read from the order's branch
            // (branches.vat_number). Left null to use that value.
            $table->string('seller_pan_override')->nullable();

            // Nepali fiscal year in the "YYYY.0YY" format CBMS expects
            // (e.g. "2082.083"). Admin-maintained since it only changes once
            // a year, at the Shrawan 1 BS fiscal year rollover.
            $table->string('fiscal_year')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cbms_settings');
    }
};
