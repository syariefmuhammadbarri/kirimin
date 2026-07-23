<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'tracking_number', 'booking_code', 'customer_id', 'branch_id', 'courier_id', 'payment_id',
    'status', 'origin_city', 'destination_city', 'sender_name', 'sender_phone', 'sender_address',
    'receiver_name', 'receiver_phone', 'receiver_address', 'estimated_weight', 'actual_weight',
    'estimated_price', 'actual_price', 'total_price', 'service_type',
    'next_branch_id', 'fulfillment_type', 'pickup_address', 'pickup_scheduled_at', 'pickup_notes',
    'cancelled_at', 'cancel_reason', 'delivery_attempt_count'
])]
class Shipment extends Model
{
    use HasFactory;

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function courier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function trackings(): HasMany
    {
        return $this->hasMany(ShipmentTracking::class)->orderBy('tracked_at', 'desc');
    }

    public function deliveryProof(): HasOne
    {
        return $this->hasOne(DeliveryProof::class);
    }

    public function courierAssignments(): HasMany
    {
        return $this->hasMany(CourierAssignment::class);
    }

    public function activeAssignment()
    {
        return $this->hasOne(CourierAssignment::class)->whereIn('status', ['assigned', 'pending'])->latestOfMany();
    }

    public function nextBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'next_branch_id');
    }

    protected function casts(): array
    {
        return [
            'pickup_scheduled_at' => 'datetime',
            'cancelled_at'        => 'datetime',
        ];
    }

    /**
     * Dispatch notification to the customer about status change.
     * This ensures ALL status changes are synced to all modules.
     */
    public function notifyStatusChange(string $status, string $description, string $location): void
    {
        if ($this->customer && $this->customer->user) {
            $this->customer->user->notify(new \App\Notifications\ShipmentStatusChanged(
                $this,
                $status,
                $description,
                $location
            ));
        }
    }

    /**
     * Kembalikan true jika shipment ini eligible untuk dibatalkan oleh customer.
     * Syarat: status masih booking_created/waiting_dropoff/pickup_scheduled DAN payment belum paid.
     */
    public function isCancellable(): bool
    {
        $cancellableStatuses = ['booking_created', 'waiting_dropoff', 'pickup_scheduled', 'payment_pending'];
        $paymentNotPaid = !$this->payment || $this->payment->payment_status !== 'paid';
        return in_array($this->status, $cancellableStatuses) && $paymentNotPaid;
    }

    /**
     * Label status bahasa Indonesia ramah pengguna.
     */
    public function getStatusLabelAttribute(): string
    {
        $map = [
            'booking_created'               => 'Booking Dibuat',
            'waiting_dropoff'               => 'Menunggu Drop-Off',
            'pickup_scheduled'              => 'Penjemputan Dijadwalkan',
            'pickup_assigned'               => 'Kurir Menjemput Paket',
            'picked_up_from_customer'       => 'Paket Terjemput',
            'weighed'                       => 'Sudah Ditimbang',
            'payment_pending'               => 'Menunggu Pembayaran',
            'received_at_branch'            => 'Diterima di Gudang Cabang',
            'in_transit'                    => 'Dalam Pengiriman (Transit)',
            'assigned_to_courier'           => 'Tugaskan Kurir Antar',
            'picked_up'                     => 'Dibawa Kurir',
            'out_for_delivery'              => 'Sedang Diantar Kurir',
            'delivery_confirmation_pending' => 'Menunggu Verifikasi Admin',
            'delivered'                     => 'Selesai (Sudah Diterima)',
            'gagal_kirim'                   => 'Gagal Dikirim',
            'cancelled'                     => 'Dibatalkan',
            'returned'                      => 'Dikembalikan',
        ];

        return $map[$this->status] ?? str_replace('_', ' ', $this->status);
    }
}
