<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChurnPrediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'loyalty_member_id',
        'predicted_probability',
        'generated_retention_offer',
        'status'
    ];

    /**
     * Define the inverse relationship to LoyaltyMember.
     */
    public function loyaltyMember(): BelongsTo
    {
        return $this->belongsTo(LoyaltyMember::class);
    }
}
