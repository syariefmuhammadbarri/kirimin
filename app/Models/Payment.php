<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['order_id', 'shipment_id', 'amount', 'payment_method', 'payment_status', 'snap_token', 'paid_amount', 'expired_at'])]
class Payment extends Model
{
    use HasFactory;

    public static function normalizePaymentMethod(?string $value): string
    {
        $normalized = strtolower(trim((string) $value));

        $map = [
            'transfer (mocked)' => 'transfer',
            'transfer_mocked' => 'transfer',
            'bank_transfer' => 'transfer',
            'credit_card' => 'transfer',
            'echannel' => 'transfer',
            'bca_klikpay' => 'transfer',
            'bca_klikbca' => 'transfer',
            'cimb_clicks' => 'transfer',
            'akulaku' => 'transfer',
            'gopay' => 'e-wallet',
            'shopeepay' => 'e-wallet',
            'qris' => 'e-wallet',
            'indomaret' => 'cash',
            'alfamart' => 'cash',
            'cash' => 'cash',
            'transfer' => 'transfer',
            'e-wallet' => 'e-wallet',
            'ewallet' => 'e-wallet',
            'midtrans' => 'midtrans',
        ];

        if (isset($map[$normalized])) {
            return $map[$normalized];
        }

        if (str_contains($normalized, 'transfer')) {
            return 'transfer';
        }

        if (str_contains($normalized, 'wallet') || str_contains($normalized, 'gopay') || str_contains($normalized, 'shopee') || str_contains($normalized, 'qris')) {
            return 'e-wallet';
        }

        if (str_contains($normalized, 'cash') || str_contains($normalized, 'indomaret') || str_contains($normalized, 'alfamart')) {
            return 'cash';
        }

        if (str_contains($normalized, 'midtrans')) {
            return 'midtrans';
        }

        return 'transfer';
    }

    public function setPaymentMethodAttribute(?string $value): void
    {
        $this->attributes['payment_method'] = self::normalizePaymentMethod($value);
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    protected function casts(): array
    {
        return [
            'expired_at' => 'datetime',
            'amount'     => 'decimal:2',
            'paid_amount'=> 'decimal:2',
        ];
    }

    /**
     * Kembalikan true jika batas waktu pembayaran sudah lewat dan payment masih pending.
     */
    public function isExpired(): bool
    {
        return $this->expired_at !== null
            && $this->expired_at->isPast()
            && $this->payment_status === 'pending';
    }
}
