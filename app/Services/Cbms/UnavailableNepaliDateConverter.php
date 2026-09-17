<?php

namespace App\Services\Cbms;

use App\Services\Cbms\Contracts\NepaliDateConverter;
use Illuminate\Support\Carbon;

/**
 * Default NepaliDateConverter binding: deliberately refuses to guess.
 *
 * I could not obtain or independently verify an authoritative AD<->BS
 * (Bikram Sambat) calendar data table in the environment this integration
 * was built in - the BS calendar's month lengths are published year by year
 * by the Nepal government and are not derivable from a formula, so a wrong
 * table would silently submit incorrect invoice/credit-note dates to a tax
 * authority. That's a worse failure than refusing to submit at all.
 *
 * Before going live, replace this binding (see config/services.php ->
 * cbms.date_converter) with an implementation backed by a verified
 * conversion table - e.g. a maintained Nepali-date Composer package, or the
 * official Nepal calendar data - wired up as its own class implementing
 * NepaliDateConverter.
 */
class UnavailableNepaliDateConverter implements NepaliDateConverter
{
    public function toBsDate(Carbon $date): string
    {
        throw new \RuntimeException(
            'No verified Bikram Sambat date converter is configured for CBMS. ' .
            'Set services.cbms.date_converter (config/services.php) to a class ' .
            'implementing App\\Services\\Cbms\\Contracts\\NepaliDateConverter backed ' .
            'by a verified AD<->BS calendar table before enabling CBMS sync.'
        );
    }
}
