<?php

namespace App\Services\Cbms;

use App\Models\CbmsSetting;
use App\Models\Order;
use App\Services\Cbms\Contracts\NepaliDateConverter;

/**
 * Builds the JSON payloads documented in the CBMS API spec from an Order.
 *
 * Amount mapping: the doc's own sample (total_sales=1130, taxable_sales_vat
 * =1000, vat=130) shows total_sales = taxable_sales_vat + vat, i.e.
 * total_sales is the grand total including VAT. This app only tracks a
 * single aggregate tax amount per order (Order::total_tax_amount), so - as
 * is standard for a Nepali restaurant bill - it is reported entirely as
 * VAT. excise/HST/ESF are not applicable to restaurant billing and are
 * always sent as 0. An order with no tax charged (e.g. below VAT threshold)
 * is reported as tax_exempted_sales instead.
 */
class CbmsPayloadBuilder
{
    public function __construct(private readonly NepaliDateConverter $dateConverter)
    {
    }

    public function forBill(Order $order, CbmsSetting $setting): array
    {
        $amounts = $this->resolveAmounts($order);
        $branch = $order->branch;

        return array_merge([
            'username' => (string) $setting->username,
            'password' => (string) $setting->password,
            'seller_pan' => (string) $this->resolveSellerPan($setting, $branch),
            'buyer_pan' => '',
            'fiscal_year' => (string) $setting->fiscal_year,
            'buyer_name' => (string) ($order->customer->name ?? ''),
            'invoice_number' => (string) ($order->formatted_order_number ?? $order->order_number),
            'invoice_date' => $this->dateConverter->toBsDate($order->date_time ?? $order->created_at),
            'isrealtime' => true,
            'datetimeClient' => now()->toIso8601String(),
        ], $amounts);
    }

    public function forCreditNote(Order $order, CbmsSetting $setting, string $creditNoteNumber, string $reason): array
    {
        $amounts = $this->resolveAmounts($order);
        $branch = $order->branch;

        return array_merge([
            'username' => (string) $setting->username,
            'password' => (string) $setting->password,
            'seller_pan' => (string) $this->resolveSellerPan($setting, $branch),
            'buyer_pan' => '',
            'fiscal_year' => (string) $setting->fiscal_year,
            'buyer_name' => (string) ($order->customer->name ?? ''),
            'ref_invoice_number' => (string) ($order->formatted_order_number ?? $order->order_number),
            'credit_note_number' => $creditNoteNumber,
            'credit_note_date' => $this->dateConverter->toBsDate(now()),
            'reason_for_return' => $reason !== '' ? $reason : 'Order cancelled',
            'isrealtime' => true,
            'datetimeClient' => now()->toIso8601String(),
        ], $amounts);
    }

    private function resolveSellerPan(CbmsSetting $setting, $branch): string
    {
        return $setting->seller_pan_override ?: (string) ($branch->vat_number ?? '');
    }

    private function resolveAmounts(Order $order): array
    {
        $total = round((float) ($order->total ?? 0), 2);
        $vat = round((float) ($order->total_tax_amount ?? 0), 2);

        $base = [
            'excisable_amount' => 0,
            'excise' => 0,
            'taxable_sales_hst' => 0,
            'hst' => 0,
            'amount_for_esf' => 0,
            'esf' => 0,
            'export_sales' => 0,
        ];

        if ($vat > 0) {
            return array_merge($base, [
                'total_sales' => $total,
                'taxable_sales_vat' => round($total - $vat, 2),
                'vat' => $vat,
                'tax_exempted_sales' => 0,
            ]);
        }

        return array_merge($base, [
            'total_sales' => $total,
            'taxable_sales_vat' => 0,
            'vat' => 0,
            'tax_exempted_sales' => $total,
        ]);
    }
}
