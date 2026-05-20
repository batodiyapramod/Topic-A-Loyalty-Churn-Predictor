<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyMember extends Model
{
    use HasFactory;

    /**
     * A member can have multiple historical predictions.
     */
    public function churnPredictions(): HasMany
    {
        return $this->hasMany(ChurnPrediction::class);
    }
}
