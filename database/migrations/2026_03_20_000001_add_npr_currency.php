<?php

use App\Models\Currency;
use App\Models\GlobalCurrency;
use App\Models\Restaurant;
use App\Scopes\RestaurantScope;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $npr = GlobalCurrency::firstOrCreate([
            'currency_code' => 'NPR'
        ], [
            'currency_name' => 'Nepalese Rupee',
            'currency_symbol' => 'Rs',
            'currency_code' => 'NPR',
            'currency_position' => 'left',
            'no_of_decimal' => 2,
            'thousand_separator' => ',',
            'decimal_separator' => '.',
        ]);

        Restaurant::withoutGlobalScope(RestaurantScope::class)->get()->each(function (Restaurant $restaurant) use ($npr) {
            $exists = Currency::withoutGlobalScope(RestaurantScope::class)
                ->where('restaurant_id', $restaurant->id)
                ->where('currency_code', 'NPR')
                ->exists();

            if (!$exists) {
                $currency = new Currency();
                $currency->currency_name = $npr->currency_name;
                $currency->currency_symbol = $npr->currency_symbol;
                $currency->currency_code = $npr->currency_code;
                $currency->restaurant_id = $restaurant->id;
                $currency->saveQuietly();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Currency::withoutGlobalScope(RestaurantScope::class)
            ->where('currency_code', 'NPR')
            ->get()
            ->each(fn (Currency $currency) => $currency->deleteQuietly());

        GlobalCurrency::where('currency_code', 'NPR')->delete();
    }
};
