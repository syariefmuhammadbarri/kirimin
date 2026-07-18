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
    'next_branch_id', 'fulfillment_type', 'pickup_address', 'pickup_scheduled_at', 'pickup_notes'
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
        ];
    }
}
