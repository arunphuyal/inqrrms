<?php

namespace App\Services\Cbms\Contracts;

use Illuminate\Support\Carbon;

/**
 * Converts an AD (Gregorian) date to the Bikram Sambat (BS) "YYYY.MM.DD"
 * string that CBMS's invoice_date / credit_note_date fields expect - see the
 * sample payload in the IRD CBMS API documentation, e.g. invoice_date =
 * "2074.07.06".
 *
 * The BS calendar's month lengths are not derived from a formula - they are
 * published year by year by the Nepal government - so any implementation of
 * this contract needs a verified calendar data table, not an approximation.
 */
interface NepaliDateConverter
{
    /**
     * @throws \RuntimeException if the date falls outside the converter's
     *  supported range, or no verified conversion is configured.
     */
    public function toBsDate(Carbon $date): string;
}
