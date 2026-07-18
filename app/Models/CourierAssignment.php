<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourierAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'courier_id',
        'assigned_by',
        'assigned_at',
        'status',
        'notes',
        'type',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function courier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    public function assignor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}