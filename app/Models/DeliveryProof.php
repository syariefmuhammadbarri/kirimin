<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['shipment_id', 'courier_id', 'photos', 'notes', 'recipient_name', 'recipient_signature', 'admin_status', 'admin_notes', 'reviewed_by', 'reviewed_at'])]
class DeliveryProof extends Model
{
    use HasFactory;

    protected $table = 'delivery_proofs';

    protected function casts(): array
    {
        return [
            'photos' => 'array',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function courier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'courier_id');
    }
}
