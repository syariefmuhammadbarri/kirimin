<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['origin_city', 'destination_city', 'price_per_kg', 'estimated_days'])]
class Rate extends Model
{
    use HasFactory;
}
